<div class="card card-primary">
    <div class="card-header ">
        <h3 class="card-title">{title}</h3>
    </div>

    <!-- /.card-header -->
    <div class="card-body table-responsive p-0">
        <div class="card-tools" style="padding:5px;">
            <button type="button" class="btn bg-green"><i class="fas fa-plus"></i> 新增</button>
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
                    <th>ID</th>
                    <th>User</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Reason</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>183</td>
                    <td>John Doe</td>
                    <td>11-7-2014</td>
                    <td><span class="tag tag-success">Approved</span></td>
                    <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
                <tr>
                    <td>219</td>
                    <td>Alexander Pierce</td>
                    <td>11-7-2014</td>
                    <td><span class="tag tag-warning">Pending</span></td>
                    <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
                <tr>
                    <td>657</td>
                    <td>Bob Doe</td>
                    <td>11-7-2014</td>
                    <td><span class="tag tag-primary">Approved</span></td>
                    <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
                <tr>
                    <td>175</td>
                    <td>Mike Doe</td>
                    <td>11-7-2014</td>
                    <td><span class="tag tag-danger">Denied</span></td>
                    <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                </tr>
            </tbody>
        </table>
    </div>
    <!-- /.card-body -->
</div>
<script>
    $('document').ready(function() {
        $('div[widget="{code}"]').backyard_data({
            'userType': 'master'
        });
    });
</script>
<!-- /.card -->