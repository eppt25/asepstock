

  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <h1>
        ผู้ใช้
        <small>โปรไฟล์</small>
      </h1>
    </section>

    <!-- Main content -->
    <section class="content">
      <!-- Small boxes (Stat box) -->
      <div class="row">
        <div class="col-md-12 col-xs-12">

          <div class="box">
            <div class="box-header">
              <h3 class="box-title">โปรไฟล์</h3>
            </div>
            <!-- /.box-header -->
            <div class="box-body">
              <table class="table table-bordered table-condensed table-hovered">
                <tr>
                  <th>ชื่อผู้ใช้</th>
                  <td><?php echo $user_data['username']; ?></td>
                </tr>
                <tr>
                  <th>อีเมล</th>
                  <td><?php echo $user_data['email']; ?></td>
                </tr>
                <tr>
                  <th>ชื่อจริง</th>
                  <td><?php echo $user_data['firstname']; ?></td>
                </tr>
                <tr>
                  <th>นามสกุล</th>
                  <td><?php echo $user_data['lastname']; ?></td>
                </tr>
                <tr>
                  <th>เพศ</th>
                  <td><?php echo ($user_data['gender'] == 1) ? 'Male' : 'Gender'; ?></td>
                </tr>
                <tr>
                  <th>เบอร์โทร</th>
                  <td><?php echo $user_data['phone']; ?></td>
                </tr>
                <tr>
                  <th>สิทธิ</th>
                  <td><span class="label label-info"><?php echo $user_group['group_name']; ?></span></td>
                </tr>
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

 
