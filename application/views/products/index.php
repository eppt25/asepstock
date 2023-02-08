

<!-- Content Wrapper. Contains page content -->
<div class="content-wrapper">
  <!-- Content Header (Page header) -->
  <section class="content-header">
    <h1>
      จัดการ
      <small>สินค้า</small>
    </h1>
  </section>

  <!-- Main content -->
  <section class="content">
    <!-- Small boxes (Stat box) -->
    <div class="row">
      <div class="col-md-12 col-xs-12">

        <div id="messages"></div>

        <?php if($this->session->flashdata('success')): ?>
          <div class="alert alert-success alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('success'); ?>
          </div>
        <?php elseif($this->session->flashdata('error')): ?>
          <div class="alert alert-error alert-dismissible" role="alert">
            <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            <?php echo $this->session->flashdata('error'); ?>
          </div>
        <?php endif; ?>

        <?php if(in_array('createProduct', $user_permission)): ?>
          <a href="<?php echo base_url('products/create') ?>" class="btn btn-primary">เพิ่มสินค้า</a>
        <?php endif; ?>


        
        <br /> <br />

        <div class="box">
          <div class="box-header">
            <h3 class="box-title">จัดการสินค้า</h3>
            <div class="text-right">
              <?php if(in_array('viewProduct', $user_permission)): ?>
                <button type="button" class="btn btn-primary" onclick="filterByStore('AS')">โกดัง AS</button>
                <button type="button" class="btn btn-primary" onclick="filterByStore('EP')">โกดัง EP</button>
              <?php endif; ?>

              <?php if(in_array('viewProduct', $user_permission)): ?>
                <button type="button" class="btn btn-primary" onclick="clearFilters('EP')">ล้างการค้นหา</button>
              <?php endif; ?>     
            </div>
          </div>
          <!-- /.box-header -->
          <div class="box-body">
            <table id="manageTable" class="table table-bordered table-striped">
              <thead>
              <tr>
                <th>รูป</th>
                <th>ชื่อสินค้า</th>
                <th>ราคา</th>
                <th>จำนวน</th>
                <th>โกดังสินค้า</th>
                <th>สถานะ</th>
                <?php if(in_array('updateProduct', $user_permission) || in_array('deleteProduct', $user_permission) || in_array('increaseProduct', $user_permission)): ?>
                  <th>จัดการ</th>
                <?php endif; ?>
              </tr>
              </thead>

            </table>
          </div>
          <!-- /.box-body -->
        </div>
        <!-- /.box -->
      </div>
      <!-- col-md-12 -->
    </div>
    <!-- /.row -->
    

  </section>
  <!-- /.content -->
</div>
<!-- /.content-wrapper -->

<?php if(in_array('updateProduct', $user_permission)): ?>
<!-- Increase Quantity Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="increaseQuantityModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">เพิ่มจำนวนสินค้า</h4>
      </div>

      <form action="<?php echo base_url('products/increaseQuantity') ?>" method="post" id="increaseQuantityForm">
        <div class="modal-body">
          <div class="form-group">
            <label for="quantity">จำนวน</label>
            <input type="hidden" name="product_id_increase" id="product_id_increase" value="">
            <input type="number" class="form-control" id="quantity_increase" name="quantity_increase" min="1">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
          <button type="submit" class="btn btn-primary">เพิ่มจำนวนสินค้า</button>
        </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php endif; ?>

<?php if(in_array('updateProduct', $user_permission)): ?>
<!-- Decrease Quantity Modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="decreaseQuantityModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">ลดจำนวนสินค้า</h4>
      </div>

      <form action="<?php echo base_url('products/decreaseQuantity') ?>" method="post" id="decreaseQuantityForm">
        <div class="modal-body">
          <div class="form-group">
            <label for="quantity">จำนวน</label>
            <input type="hidden" name="product_id_decrease" id="product_id_decrease" value="">
            <input type="number" class="form-control" id="quantity_decrease" name="quantity_decrease" min="1">
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
          <button type="submit" class="btn btn-primary">ลดจำนวนสินค้า</button>
        </div>
      </form>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php endif; ?>

<?php if(in_array('deleteProduct', $user_permission)): ?>
<!-- remove brand modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="removeModal">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">ลบสินค้า</h4>
      </div>

      <form role="form" action="<?php echo base_url('products/remove') ?>" method="post" id="removeForm">
        <div class="modal-body">
          <p>คุณต้องการจะลบจริงหรือไม่ ?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-default" data-dismiss="modal">ปิด</button>
          <button type="submit" class="btn btn-primary">ลบ</button>
        </div>
      </form>


    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<?php endif; ?>

<script type="text/javascript">
var manageTable;
var base_url = "<?php echo base_url(); ?>";

$(document).ready(function() {

  $("#mainProductNav").addClass('active');

  // initialize the datatable 
  manageTable = $('#manageTable').DataTable({
    // 'ajax': base_url + 'products/fetchProductData',
    'ajax': base_url + 'Products/fetchProductData',
    'order': []
  });

});

$('#increaseQuantityForm').submit(function(e) {
    e.preventDefault();

    // submit the form via Ajax
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {
          manageTable.ajax.reload(null, false);
          if (response.success == true) {
            $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
            '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
            '</div>');

            $('#increaseQuantityModal').modal('hide');

          } else {
            $("#messages").html('<div class="alert alert-warning alert-dismissible" role="alert">'+
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
              '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>'+response.messages+
            '</div>');
          }
        },
        complete: function() {
            // hide the modal
            $('#increaseQuantityModal').modal('hide');
        }
    });
});

// filter function
function filterByStore(warehouse) {
  manageTable.column(4).search(warehouse);
  manageTable.draw();
}

// clear filter function
function clearFilters() {
    manageTable.search('').columns().search('').draw();
}

// increase functions
function increaseQuantity(id) {
  $('#product_id_increase').val(id);
  $('#increaseQuantityModal').on('hidden.bs.modal', function () {
    setTimeout(function() {
      $('#increaseQuantityModal').modal('hide');
    }, 1000); // delay in milliseconds
  });
}

$('#decreaseQuantityForm').submit(function(e) {
    e.preventDefault();

    // submit the form via Ajax
    $.ajax({
        url: $(this).attr('action'),
        type: 'POST',
        data: $(this).serialize(),
        dataType: 'json',
        success: function(response) {

          manageTable.ajax.reload(null, false);
          
          if (response.success == true) {
            $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
            '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
            '</div>');

            $('#decreaseQuantityModal').modal('hide');
          } else {
            $("#messages").html('<div class="alert alert-warning alert-dismissible" role="alert">'+
            '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
            '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>'+response.messages+
            '</div>');
          }
        },
        complete: function() {
            // hide the modal
            $('#decreaseQuantityModal').modal('hide');
        }
    });
});

// decrease functions
function decreaseQuantity(id) {
  $('#product_id_decrease').val(id);
  $('#decreaseQuantityModal').on('hidden.bs.modal', function () {
    setTimeout(function() {
      $('#decreaseQuantityModal').modal('hide');
    }, 1000); // delay in milliseconds
  });
}

// remove functions 
function removeFunc(id)
{
  if(id) {
    $("#removeForm").on('submit', function() {

      var form = $(this);

      // remove the text-danger
      $(".text-danger").remove();

      $.ajax({
        url: form.attr('action'),
        type: form.attr('method'),
        data: { product_id:id }, 
        dataType: 'json',
        success:function(response) {

          manageTable.ajax.reload(null, false); 

          if(response.success === true) {
            $("#messages").html('<div class="alert alert-success alert-dismissible" role="alert">'+
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
              '<strong> <span class="glyphicon glyphicon-ok-sign"></span> </strong>'+response.messages+
            '</div>');

            // hide the modal
            $("#removeModal").modal('hide');

          } else {

            $("#messages").html('<div class="alert alert-warning alert-dismissible" role="alert">'+
              '<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>'+
              '<strong> <span class="glyphicon glyphicon-exclamation-sign"></span> </strong>'+response.messages+
            '</div>'); 
          }
        }
      }); 

      return false;
    });
  }
}


</script>
