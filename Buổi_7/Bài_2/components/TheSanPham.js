export default {
  name: 'TheSanPham',
  props: {
    ten: {
      type: String,
      required: true 
    },
    gia: {
      type: Number,
      required: true
    },
    anh: {
      type: String,
      default: 'https://via.placeholder.com/150' 
    },
    moTa: {
      type: String,
      default: 'Chưa có mô tả chi tiết cho sản phẩm này.'
    },
    soLuongTon: {
      type: Number,
      required: true
    }
  },
  data() {
    return {
      hienMoTa: false 
    };
  },
  methods: {
    toggleMoTa() {
      this.hienMoTa = !this.hienMoTa;
    }
  },
  template: `
    <div class="card">
      <img :src="anh" :alt="ten" class="card-img">
      <h3>{{ ten }}</h3>
      <p class="price">Giá: {{ gia.toLocaleString() }} VNĐ</p>
      
      <div v-if="soLuongTon === 0" class="badge-out">Hết hàng</div>
      <div v-else class="badge-in">Còn tồn: {{ soLuongTon }}</div>

      <div style="margin-top: 10px;">
        <button @click="toggleMoTa">
          {{ hienMoTa ? 'Ẩn mô tả' : 'Xem mô tả' }}
        </button>
        <button :disabled="soLuongTon === 0" style="margin-left: 5px;">Mua</button>
      </div>

      <p v-if="hienMoTa" class="description">{{ moTa }}</p>
    </div>
  `
};
