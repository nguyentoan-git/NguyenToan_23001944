const { createApp } = Vue;

createApp({
  data() {
    return {
      status: 'Khởi tạo...',
      searchKey: '',
      searchNotice: '',
      selectedCategory: '',
      sortOrder: 'asc',
      errorMessage: '',
      categories: ['Điện tử', 'Gia dụng', 'Thời trang'],
      products: [
        { id: 1, name: 'Điện thoại iPhone 15', price: 22000000, category: 'Điện tử', stock: 5 },
        { id: 2, name: 'Laptop Dell XPS', price: 35000000, category: 'Điện tử', stock: 0 },
        { id: 3, name: 'Nồi chiên không dầu', price: 2500000, category: 'Gia dụng', stock: 12 },
        { id: 4, name: 'Máy hút bụi Smart', price: 4200000, category: 'Gia dụng', stock: 3 },
        { id: 5, name: 'Áo sơ mi nam', price: 350000, category: 'Thời trang', stock: 20 },
        { id: 6, name: 'Quần Jeans nữ', price: 500000, category: 'Thời trang', stock: 0 }
      ],
      newProduct: { name: '', price: 0, category: 'Điện tử', stock: 0 }
    };
  },
  computed: {
    filteredProducts() {
      let result = [...this.products];

      // 1. Tìm kiếm theo tên (không phân biệt hoa/thường)
      if (this.searchKey.trim() !== '') {
        const keyword = this.searchKey.toLowerCase().trim();
        result = result.filter(p => p.name.toLowerCase().includes(keyword));
      }

      // 2. Lọc theo danh mục
      if (this.selectedCategory !== '') {
        result = result.filter(p => p.category === this.selectedCategory);
      }

      // 3. Sắp xếp theo giá
      result.sort((a, b) => {
        return this.sortOrder === 'asc' ? a.price - b.price : b.price - a.price;
      });

      return result;
    },
    statistics() {
      const displayCount = this.filteredProducts.length;
      const totalCount = this.products.length;
      const totalValue = this.filteredProducts.reduce((sum, p) => sum + (p.price * p.stock), 0);
      return { displayCount, totalCount, totalValue };
    }
  },
  watch: {
    searchKey(newVal) {
      if (newVal.length === 1) {
        this.searchNotice = 'Vui lòng nhập từ 2 ký tự trở lên để tìm kiếm chính xác hơn!';
      } else {
        this.searchNotice = '';
      }
    }
  },
  mounted() {
    this.status = 'Sẵn sàng';
  },
  methods: {
    addProduct() {
      this.errorMessage = '';
      if (!this.newProduct.name.trim()) {
        this.errorMessage = 'Tên sản phẩm không được rỗng!';
        return;
      }
      if (this.newProduct.price <= 0) {
        this.errorMessage = 'Giá phải lớn hơn 0!';
        return;
      }
      if (!Number.isInteger(this.newProduct.stock) || this.newProduct.stock < 0) {
        this.errorMessage = 'Số lượng tồn phải là số nguyên không âm!';
        return;
      }

      const nextId = this.products.length > 0 ? Math.max(...this.products.map(p => p.id)) + 1 : 1;
      this.products.push({
        id: nextId,
        name: this.newProduct.name,
        price: this.newProduct.price,
        category: this.newProduct.category,
        stock: this.newProduct.stock
      });
      
      this.newProduct = { name: '', price: 0, category: 'Điện tử', stock: 0 };
    },
    deleteProduct(id) {
      if (confirm('Bạn có chắc chắn muốn xóa sản phẩm này không?')) {
        this.products = this.products.filter(p => p.id !== id);
      }
    }
  }
}).mount('#app');
