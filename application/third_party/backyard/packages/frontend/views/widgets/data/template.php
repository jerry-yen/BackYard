<div class="card card-primary {code}_table">
    <div class="card-header ">
        <h3 class="card-title">{title}</h3>
    </div>

    <!-- /.card-header -->
    <div class="card-body table-responsive p-0">
        <div class="card-tools" style="padding:5px;">
            <button type="button" class="add btn bg-green"><i class="fas fa-plus"></i> 新增</button>
            <button type="button" class="btn bg-red"><i class="fas fa-trash-alt"></i> 刪除</button>
            <button type="button" class="btn bg-gradient-info"><i class="fas fa-sort-amount-down-alt"></i> 排序</button>
            <div class="btn-group">
                <button type="button" class="btn bg-yellow"><i class="fas fa-bars"></i> 其它</button>
                <button type="button" class="btn bg-yellow dropdown-toggle dropdown-icon" data-toggle="dropdown">
                    <span class="sr-only">Toggle Dropdown</span>
                </button>
                <div class="dropdown-menu" role="menu">
                    <a class="dropdown-item" href="#"><i class="fas fa-download"></i> 匯出</a>
                    <div class="dropdown-divider"></div>
                    <a class="dropdown-item" href="#"><i class="fas fa-upload"></i> 匯入</a>
                </div>
            </div>
        </div>
        <table class="table table-hover text-nowrap">
            <thead>
                <tr>
                    <th>操作</th>
                    <th>User</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <tr class="d-none">
                    <td>
                        <button type="button" class="modify btn btn-xs bg-blue"><i class="far fa-edit"></i> 修改</button>
                        <button type="button" class="btn btn-xs bg-red"><i class="fas fa-trash-alt"></i> 刪除</button>
                        <button type="button" class="btn btn-xs bg-gradient-info"><i class="fas fa-bars"></i> 瀏覽</button>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
    <div class="card-footer clearfix">
        <ul class="pagination pagination-sm m-0 float-right">
            <li class="page-item"><a class="page-link" href="#">&laquo;</a></li>
            <li class="page-item"><a class="page-link" href="#">1</a></li>
            <li class="page-item"><a class="page-link" href="#">2</a></li>
            <li class="page-item"><a class="page-link" href="#">3</a></li>
            <li class="page-item"><a class="page-link" href="#">&raquo;</a></li>
        </ul>
    </div>
</div>

<!-- general form elements -->
<div class="card card-primary {code}_form d-none">
    <div class="card-header">
        <h3 class="card-title"></h3>
    </div>
    <!-- /.card-header -->
    <!-- form start -->
    <form role="form">
        <div class="card-body">
            
        </div>
        <!-- /.card-body -->

        <div class="card-footer">
            <button type="button" class="submit btn btn-primary">儲存</button>
        </div>
    </form>
</div>
<!-- /.card -->
<script>
    $('document').ready(function() {
        $('div[widget="{code}"]').backyard_data({
            'userType': 'master'
        });
    });
</script>
<!-- /.card -->