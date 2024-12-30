document.getElementById("add").addEventListener("click", function () {
  alert("Thêm mới hàng hóa!");
});
document.getElementById("loai_xuat").addEventListener("change", function () {
  if (this.value == "Cửa hàng khác") {
    document.getElementById("cua_hang_div").style.display = "block";
    document.getElementById("ly_do_huy_div").style.display = "none";
  } else {
    document.getElementById("cua_hang_div").style.display = "none";
    document.getElementById("ly_do_huy_div").style.display = "block";
  }
});
