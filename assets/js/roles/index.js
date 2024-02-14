// Cargar lista paginable
function loadData() {
    $(document).ready(function () {
        $("#roles").dataTable().fnDestroy();
        $('#roles').dataTable({
            "lengthMenu": [[10, 25, 50, 75, 100], [10, 25, 50, 75, 100]],
            'paging': true,
            'info': true,
            'filter': true,
            'stateSave': true,
            'processing': true,
            'serverSide': true,
            'ajax': {
                "url": base_url + "admin/roles/ajax_roles",
                "type": "POST"
            },
            'columns': [
                { data: 'id', 'sClass': 'text-right'},
                {data: 'name'},
                {
                    'cell': true,
                    render: function (data, type, row) {
                        const VIEW_URL = `${base_url}admin/roles/view/${row.id}`;
                        const EDIT_URL = `${base_url}admin/roles/edit/${row.id}`;
                        const DELETE_URL = `${base_url}admin/roles/delete/${row.id}`;
                        const BTN_VIEW = (ROLE_READ)?`<a class="btn btn-info btn-circle btn-sm" href="${VIEW_URL}"><i class="fas fa-info-circle" title="Ver"></i></a>`:'';
                        const BTN_UPDATE = (ROLE_UPDATE)?`<a class="btn btn-warning btn-circle btn-sm" href="${EDIT_URL}"><i class="fas fa-edit fa-sm" title="Editar"></i></a>`:'';
                        const BTN_DELETE = (ROLE_DELETE)?`<button class="btn btn-danger btn-circle btn-sm" onClick="return deleteConfirmation('CONFIRMACIÓN', 'Se elimará el rol (${row.name})', '${DELETE_URL}')"><i class="fas fa-trash" title="Eliminar"></i></button>`:'';
                    return `
                        ${BTN_VIEW}
                        ${BTN_UPDATE}
                        ${BTN_DELETE}
                    `;  
                    },
                    'sClass': 'text-center'
                }
            ],
            "order": [[0, "asc"]]
        });
    });

}

loadData();

