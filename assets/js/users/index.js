// Cargar lista paginable
function loadData() {
    $(document).ready(function () {
        $("#users").dataTable().fnDestroy();
        $('#users').dataTable({
            "lengthMenu": [[10, 25, 50, 75, 100], [10, 25, 50, 75, 100]],
            'paging': true,
            'info': true,
            'filter': true,
            'stateSave': true,
            'processing': true,
            'serverSide': true,
            'ajax': {
                "url": base_url + "admin/users/ajax_users",
                "type": "POST"
            },
            'columns': [
                { data: 'id', sClass: 'text-center' },
                { 
                    'cell': true,
                    render: function (data, type, row) {
                    return `
                        ${row.first_name} ${row.last_name} 
                    `;
                        
                    }
                },
                {data: 'roles'},
                { data: 'email' },
                {
                    'cell': true,
                    render: function (data, type, row) {
                        const VIEW_URL = `${base_url}admin/users/view/${row.id}`;
                        const EDIT_URL = `${base_url}admin/users/edit/${row.id}`;
                        const DELETE_URL = `${base_url}admin/users/delete/${row.id}`;
                        const BTN_VIEW = (USER_READ)?`<a class="btn btn-info btn-circle btn-sm" href="${VIEW_URL}"><i class="fas fa-info-circle" title="Ver"></i></a>`:'';
                        const BTN_UPDATE = (USER_UPDATE)?`<a class="btn btn-warning btn-circle btn-sm" href="${EDIT_URL}"><i class="fas fa-edit fa-sm" title="Editar"></i></a>`:'';
                        const BTN_DELETE = (USER_DELETE)?`<button class="btn btn-danger btn-circle btn-sm" onClick="return deleteConfirmation('CONFIRMACIÓN', 'Se elimará el usuario (${row.first_name}) y sus creaciones como: clientes, préstamos, cajas, etc', '${DELETE_URL}')"><i class="fas fa-trash" title="Eliminar"></i></button>`:'';
                    return `
                        ${BTN_VIEW}
                        ${BTN_UPDATE}
                        ${BTN_DELETE}
                    `;  
                    }, sClass: 'text-center'
                }
            ],
            "order": [[0, "asc"]]
        });
    });

}

loadData();

