@extends('admin.base')

@section('content')
<div class="layui-card">

    <div class="layui-card-header layuiadmin-card-header-auto">
        <div class="layui-btn-group">
            @can('admin.category.destroy')
            <button class="layui-btn layui-btn-sm layui-btn-danger" id="listDelete">删 除</button>
            @endcan
            @can('admin.category.create')
            <a class="layui-btn layui-btn-sm" href="{{ route('admin.category.create') }}">添 加</a>
            @endcan
        </div>
        <div class="layui-form">
            <div class="layui-input-inline">
                <input type="text" name="name" id="name" placeholder="英文名称搜索" class="layui-input">
            </div>
            <div class="layui-input-inline">
                <input type="text" name="name_cn" id="name_cn" placeholder="中文名称搜索" class="layui-input">
            </div>
            &nbsp;&nbsp;<button class="layui-btn layui-btn-sm" id="search">搜索</button>
        </div>
    </div>

    <div class="layui-card-body">
        <table id="dataTable" lay-filter="dataTable"></table>
        <script type="text/html" id="options">
            <div class="layui-btn-group">
                @can('admin.category.edit')
                <a class="layui-btn layui-btn-sm" lay-event="edit">编辑</a>
                @endcan
                @can('admin.category.destroy')
                <a class="layui-btn layui-btn-danger layui-btn-sm " lay-event="del">删除</a>
                @endcan
            </div>
        </script>
    </div>

</div>
@endsection

@section('script')

<script>
    layui.use(['layer','table','form'],function () {
        var layer = layui.layer;
        var form = layui.form;
        var table = layui.table;

        //用户表格初始化
        var dataTable = table.render({
            elem: '#dataTable'
            ,height: 700
            ,url: "{{ route('admin.category.data') }}" //数据接口
            ,page: true //开启分页
            ,cols: [[ //表头
                {checkbox: true,fixed: true}
                ,{field: 'id', title: 'ID', sort: true,width:80}
                ,{field: 'name', title: '英文名称'}
                ,{field: 'name_cn', title: '中文名称'}
                ,{field: 'author', title: '添加人'}
                ,{field: 'created_at', title: '创建时间'}
                ,{field: 'updated_at', title: '更新时间'}
                ,{fixed: 'right', width: 320, align:'center', toolbar: '#options'}
            ]]
        });

        //监听工具条
        table.on('tool(dataTable)', function(obj){ //注：tool是工具条事件名，dataTable是table原始容器的属性 lay-filter="对应的值"
            var data = obj.data //获得当前行数据
                ,layEvent = obj.event; //获得 lay-event 对应的值
            if(layEvent === 'del'){
                layer.confirm('确认删除吗？', function(index){
                    $.post("{{ route('admin.category.destroy') }}",{_method:'delete',ids:[data.id]},function (result) {
                        if (result.code==0){
                            obj.del(); //删除对应行（tr）的DOM结构
                        }
                        layer.close(index);
                        layer.msg(result.msg,{icon:6})
                    });
                });
            } else if(layEvent === 'edit'){
                location.href = '/admin/category/'+data.id+'/edit';
            }
        });

        //按钮批量删除
        $("#listDelete").click(function () {
            var ids = []
            var hasCheck = table.checkStatus('dataTable')
            var hasCheckData = hasCheck.data
            if (hasCheckData.length>0){
                $.each(hasCheckData,function (index,element) {
                    ids.push(element.id)
                })
            }
            if (ids.length>0){
                layer.confirm('确认删除吗？', function(index){
                    $.post("{{ route('admin.category.destroy') }}",{_method:'delete',ids:ids},function (result) {
                        if (result.code==0){
                            dataTable.reload()
                        }
                        layer.close(index);
                        layer.msg(result.msg,{icon:6})
                    });
                })
            }else {
                layer.msg('请选择删除项',{icon:5})
            }
        })

        // 搜索
        $("#search").click(function () {
            var name = $("#name").val();
            var name_cn = $("#name_cn").val();
            dataTable.reload({
                where:{name:name,name_cn:name_cn},
                page:{curr:1}
            })
        })
    })
</script>

@endsection

