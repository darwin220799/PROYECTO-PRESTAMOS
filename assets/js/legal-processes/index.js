// Cargar lista paginable
function loadData() {
    $(document).ready(function () {
        $("#legal-precesses").dataTable().fnDestroy();
        $('#legal-precesses').dataTable({
            "lengthMenu": [[10, 25, 50, 75, 100], [10, 25, 50, 75, 100]],
            'paging': true,
            'info': true,
            'filter': true,
            'stateSave': true,
            'processing': true,
            'serverSide': true,
            'ajax': {
                "url": base_url + "admin/legalprocesses/ajax_legal_processes",
                "type": "POST"
            },
            'columns': [
                { data: 'id', 'sClass': 'text-right' },
                { data: 'customer'},
                { data: 'start_date'},
                {
                    'cell': true,
                    render: function (data, type, row) {
                        const VIEW_URL = `${base_url}admin/legalprocesses/view/${row.id}`;
                        const DELETE_URL = `${base_url}admin/legalprocesses/delete/${row.id}`;
                        const BTN_DELETE = (LEGAL_PROCESS_DELETE)?`<button class="btn btn-danger btn-circle btn-sm" onClick="return deleteConfirmation('CONFIRMACIÓN', 'Se elimará proceso legal (${row.id})', '${DELETE_URL}')"><i class="fas fa-trash" title="Eliminar"></i></button>`:''``;
                        const BTN_VIEW = (LEGAL_PROCESS_READ)?`<a class="btn btn-info btn-circle btn-sm" href="${VIEW_URL}" title="Ver"><i class="fas fa-info-circle" title="Ver"></i></a>`:'';
                    return `
                        ${BTN_VIEW}
                        ${BTN_DELETE}
                    `;  
                    }, sClass: 'text-center'
                },
            ],
            "order": [[0, "asc"]]
        });
    });
}

if(document.getElementById( "user_id" )){
    const userSelector = document.getElementById('user_id');
    userSelector.addEventListener('change', (event) => {
        loadData();
    });
}

loadData();

