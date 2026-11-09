const { createApp } = Vue;

// Component con: DongGioHang
const DongGioHang = {
  name: 'DongGioHang',
  props: {
    item: {
      type: Object,
      required: true
    }
  },
  emits: ['doi-so-luong', 'yeu-cau-xoa'],
  computed: {
    thanhTien() {
      return this.item.donGia * this.item.soLuong;
    }
  },
  methods: {
    giam() {
      if (this.item.soLuong > 1) {
        this.$emit('doi-so-luong', { id: this.item.id, soLuongMoi: this.item.soLuong - 1 });
      }
    },
    tang() {
      if (this.item.soLuong < 99) {
        this.$emit('doi-so-luong', { id: this.item.id, soLuongMoi: this.item.soLuong + 1 });
      }
    },
    xoa() {
      this.$emit('yeu-cau-xoa', this.item.id);
    }
  },
  template: `
    <tr>
      <td style="text-align: left;">{{ item.ten }}</td>
      <td>{{ item.donGia.toLocaleString() }} đ</td>
      <td>
        <button @click="giam" :disabled="item.soLuong <= 1">-</button>
        <span style="margin: 0 8px;">{{ item.soLuong }}</span>
        <button @click="tang" :disabled="item.soLuong >= 99">+</button>
      </td>
      <td>{{ thanhTien.toLocaleString() }} đ</td>
      <td>
        <button @click="xoa" style="color: red;">Xóa</button>
      </td>
    </tr>
  `
};

// Component cha
createApp({
  components: {
    'dong-gio-hang': DongGioHang
  },
  data() {
    return {
      gioHang: [
        { id: 101, ten: 'Bút', donGia: 5000, soLuong: 2 },
        { id: 102, ten: 'Quyển vở', donGia: 12000, soLuong: 3 }
      ]
    };
  },
  computed: {
    tongSoLuong() {
      return this.gioHang.reduce((sum, item) => sum + item.soLuong, 0);
    },
    tongTien() {
      return this.gioHang.reduce((sum, item) => sum + (item.donGia * item.soLuong), 0);
    }
  },
  methods: {
    capNhatSoLuong({ id, soLuongMoi }) {
      if (Number.isInteger(soLuongMoi) && soLuongMoi >= 1 && soLuongMoi <= 99) {
        const item = this.gioHang.find(sp => sp.id === id);
        if (item) {
          item.soLuong = soLuongMoi;
        }
      }
    },
    xoaSanPham(id) {
      this.gioHang = this.gioHang.filter(item => item.id !== id);
    }
  }
}).mount('#app');
