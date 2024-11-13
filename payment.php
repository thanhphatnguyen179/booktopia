<?php ob_start(); ?>


<?php include('includes/db.php'); ?>
<?php include('includes/header.php'); ?>


<div class="main-wrapper">

<!-- Begin Hiraola's Header Main Area -->
<?php include('includes/nav_bar.php'); ?>


<?php 
// Kiểm tra xem có dữ liệu 'selected_books' từ form hay không
if (isset($_POST['selected_books'])) {
    $List_Book_ID = [];
    $ND_Ma = $_SESSION['ND_Ma'];
    // Lặp qua các mã sách đã chọn và lưu vào mảng S_Ma
    foreach ($_POST['selected_books'] as $bookId) {
        $List_Book_ID[] = $bookId;  // Thêm từng mã sách vào mảng S_Ma
    }
} else {

    echo "<script>
        Swal.fire({
            title: 'Lỗi',
            text: 'Vui lòng chọn sách mà quý khách muốn thanh toán.',
            icon: 'warning',
            confirmButtonText: 'OK'
        }).then(function() {
            window.location.href = 'cart.php';  // Chuyển hướng về trang cart.php
        });
    </script>";
    exit();  // Dừng thực thi mã PHP tiếp theo
}
?>

<?php 
    $sql_user = "SELECT `ND_HoTen`, `ND_SoDT`, `ND_Email` FROM `nguoidung` WHERE ND_Ma = '$ND_Ma'";
    $result_username = mysqli_query($connection, $sql_user);
    $row_user = mysqli_fetch_array($result_username);



?>

<div class="checkout-area">
            <div class="container">
                <div class="row">
                    <div class="col-lg-6 col-12">
                        <form action="javascript:void(0)">
                            <div class="checkbox-form">
                                <h1>Chi tiết hóa đơn</h1>
                                <hr>
                                
                                <div class="row">
                                    
                                    <div class="col-md-12">
                                        <div class="checkout-form-list">
                                            <label><h5>Họ và tên người nhận<span class="required">*</span></h5></label>
                                            <input placeholder="" type="text" value="<?php echo $row_user['ND_HoTen'] ?>">
                                        </div>
                                    </div>
                                    
                                    
                                    <div class="col-md-12">
                                        <div class="checkout-form-list">
                                            <label><h5>Email<span class="required">*</span></h5></label>
                                            <input placeholder="" type="email" value="<?php echo $row_user['ND_Email'] ?>">
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <div class="checkout-form-list">
                                            <label><h5>Số điện thoại<span class="required">*</span></h5></label>
                                            <input placeholder="" type="text" value="<?php echo $row_user['ND_SoDT'] ?>">
                                        </div>
                                    </div>
                                    <hr>
<div class="col-md-12">
<div class="sp-content">
    <h4>Giao hàng đến địa chỉ</h4>

    <!-- Radio buttons -->
    <div class="form-check">
        <input class="form-check-input" type="radio" name="addressOption" id="addressOption1" checked>
        <label class="form-check-label" for="addressOption1">
            Địa chỉ
        </label>
    </div>
    <div class="form-check">
        <input class="form-check-input" type="radio" name="addressOption" id="addressOption2">
        <label class="form-check-label" for="addressOption2">
            Chọn địa chỉ giao hàng
        </label>
    </div>

    <!-- Table for selecting address -->
    <div id="addressSelection" class="mt-3" style="display: none;">
        
        <table class="table">
            
                <tr>
                    <td>
                        <h5>Tỉnh/Thành phố</h5>
                            <select class="form-control" id="province">
                                <option value="" selected disabled>Chọn Tỉnh/Thành phố</option>
                                <!-- Options will be populated by AJAX -->
                            </select>
                        </td>
                </tr>
                <tr>
                    
                    <td>
                        <h5>Quận/Huyện</h5>
                        <select class="form-control" id="district">
                            <option value="" selected disabled>Chọn Quận/Huyện</option>
                            <!-- Options will be populated by AJAX -->
                        </select>
                    </td>
                </tr>
                <tr>
                    <td>
                    <h5>Phường xã thị trấn</h5>

                        <select class="form-control" id="ward">
                            <option value="" selected disabled>Chọn Phường/Xã/Thị trấn</option>
                            <!-- Options will be populated by AJAX -->
                        </select>
                    </td>
                </tr>
                <tr>
                    <td colspan="3">
                        <h5>Số nhà, đường </h5>
                        <input type="text" id="houseNumber" class="form-control" placeholder="Nhập số nhà">
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="mt-3">
            <h3>Chi phí vận chuyển: <span id="shippingCost" style="color:red;"></span></h3> 
        </div>
        
    </div>
</div>
</div>
                                    
                                                
                                </div>
                                
                                
                            </div>
                        </form>
                    </div>


                    <div class="col-lg-6 col-12">
                        <div class="your-order">
                            <h3>Đơn hàng</h3>
                            <div class="your-order-table table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th class="cart-product-name">Sản phẩm</th>
                                            <th class="cart-product-total">Tổng cộng</th>
                                        </tr>
                                    </thead>
                                    <tbody>
<?php 

    $mini_book_lists = [];
    
    foreach($List_Book_ID as $S_Ma) {

        echo $S_Ma;

        $sql_cart = "
            SELECT
                s.S_Ten,
                gh.GH_SoLuong,
                gny.GNY_DonGia
            FROM
                giohang gh
            JOIN sach s ON
                gh.S_Ma = s.S_Ma
            JOIN gianiemyet gny ON
                gny.S_Ma = s.S_Ma
            WHERE
                gh.KH_Ma = '$ND_Ma' AND gh.S_Ma = '$S_Ma'
            ORDER BY
                gny.GNY_NgayHieuLuc
            DESC
            LIMIT 1
        ";
        $result_cart = mysqli_query($connection, $sql_cart);
        $row_cart = mysqli_fetch_array($result_cart);
        $mini_book_lists[] = $row_cart;

    }

?>                                        
<?php 
    foreach($mini_book_lists as $list) { ?>

                              
                                        <tr class="cart_item">
                                            <td class="cart-product-name"><?php echo $list['S_Ten'] ?><strong class="product-quantity">
                                            × <?php echo $list['GH_SoLuong'] ?></strong></td>
                                            <td class="cart-product-total"><span class="amount"><?php echo $list['GNY_DonGia'] ?></span></td>
                                        </tr>     
<?php    } ?>                             
                                    </tbody>
                                    <tfoot>
                                        <tr class="cart-subtotal">
                                            <th>Cart Subtotal</th>
                                            <td><span class="amount">£215.00</span></td>
                                        </tr>
                                        <tr class="order-total">
                                            <th>Order Total</th>
                                            <td><strong><span class="amount">£215.00</span></strong></td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                            <div class="payment-method">
                                <div class="payment-accordion">
                                    <div id="accordion">
                                        <div class="card">
                                            <div class="card-header" id="#payment-1">
                                                <h5 class="panel-title">
                                                    <a href="javascript:void(0)" class="" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                        Direct Bank Transfer.
                                                    </a>
                                                </h5>
                                            </div>
                                            <div id="collapseOne" class="collapse show" data-parent="#accordion">
                                                <div class="card-body">
                                                    <p>Make your payment directly into our bank account. Please use your Order
                                                        ID as the payment
                                                        reference. Your order won’t be shipped until the funds have cleared in
                                                        our account.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header" id="#payment-2">
                                                <h5 class="panel-title">
                                                    <a href="javascript:void(0)" class="collapsed" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                        Cheque Payment
                                                    </a>
                                                </h5>
                                            </div>
                                            <div id="collapseTwo" class="collapse" data-parent="#accordion">
                                                <div class="card-body">
                                                    <p>Make your payment directly into our bank account. Please use your Order
                                                        ID as the payment
                                                        reference. Your order won’t be shipped until the funds have cleared in
                                                        our account.</p>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="card">
                                            <div class="card-header" id="#payment-3">
                                                <h5 class="panel-title">
                                                    <a href="javascript:void(0)" class="collapsed" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                        PayPal
                                                    </a>
                                                </h5>
                                            </div>
                                            <div id="collapseThree" class="collapse" data-parent="#accordion">
                                                <div class="card-body">
                                                    <p>Make your payment directly into our bank account. Please use your Order
                                                        ID as the payment
                                                        reference. Your order won’t be shipped until the funds have cleared in
                                                        our account.</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="order-button-payment">
                                        <input value="Place order" type="submit">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


























<?php include('includes/footer.php'); ?>

<?php ob_end_flush(); ?>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
    // Khi radio "Chọn địa chỉ giao hàng" được chọn, hiển thị bảng địa chỉ
    $('input[name="addressOption"]').change(function() {
        if ($('#addressOption2').is(':checked')) {
            $('#addressSelection').show();
        } else {
            $('#addressSelection').hide();
        }
    });

    // Lấy các tỉnh/thành phố khi trang được tải
    $.ajax({
        url: './includes/functions/get_provinces.php',  // URL PHP để lấy danh sách tỉnh thành
        method: 'GET',
        success: function(response) {
            const provinces = JSON.parse(response);
            $('#province').html('<option>Chọn Tỉnh/Thành phố</option>');
            provinces.forEach(function(province) {
                $('#province').append('<option value="' + province.TTP_Ma + '">' + province.TTP_Ten + '</option>');
            });
        }
    });

    // Lấy các quận/huyện khi chọn tỉnh/thành phố
    $('#province').change(function() {
        const provinceId = $(this).val();
        if (provinceId) {
            // Gọi AJAX để lấy đơn giá vận chuyển cho tỉnh này
            $.ajax({
                url: './includes/functions/get_shipping_cost.php',
                method: 'GET',
                data: { provinceId: provinceId },
                success: function(response) {
                    const data = JSON.parse(response);
                    // Hiển thị chi phí vận chuyển
                    $('#shippingCost').text(data.shippingCost + ' VND');
                }
            });

            // Gọi AJAX để lấy quận/huyện mới
            $.ajax({
                url: './includes/functions/get_districts.php',
                method: 'GET',
                data: { provinceId: provinceId },
                success: function(response) {
                    const districts = JSON.parse(response);
                    $('#district').html('<option>Chọn Quận/Huyện</option>');
                    districts.forEach(function(district) {
                        $('#district').append('<option value="' + district.QH_Ma + '">' + district.QH_Ten + '</option>');
                    });
                }
            });
        } else {
            // Nếu không có tỉnh, reset tất cả các dropdown còn lại
            $('#district').html('<option value="" selected disabled>Chọn Quận/Huyện</option>');
            $('#ward').html('<option value="" selected disabled>Chọn Phường/Xã/Thị trấn</option>');
            $('#shippingCost').text('Chi phí vận chuyển:');  // Clear shipping cost
        }
    });

    // Lấy các phường/xã khi chọn quận/huyện
    $('#district').change(function() {
        const districtId = $(this).val();
        if (districtId) {
            $.ajax({
                url: './includes/functions/get_wards.php',  // URL PHP để lấy danh sách phường xã
                method: 'GET',
                data: { districtId: districtId },
                success: function(response) {
                    const wards = JSON.parse(response);
                    $('#ward').html('<option>Chọn Phường/Xã/Thị trấn</option>');
                    wards.forEach(function(ward) {
                        $('#ward').append('<option value="' + ward.XPTT_Ma + '">' + ward.XPTT_Ten + '</option>');
                    });
                }
            });
        } else {
            // Nếu không có quận, reset lại phường/xã/ thị trấn
            $('#ward').html('<option value="" selected disabled>Chọn Phường/Xã/Thị trấn</option>');
        }
    });
});

</script>