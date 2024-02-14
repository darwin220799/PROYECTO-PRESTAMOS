// Cargar lista paginable
function loadData() {
    $(document).ready(function () {
        if(document.getElementById('user_id'))
            user_id = document.getElementById('user_id').value != null?'/'+document.getElementById('user_id').value:'';
        else
            user_id = '';
        $("#customersTable").dataTable().fnDestroy();
        $('#customersTable').dataTable({
            "lengthMenu": [[10, 25, 50, 75, 100], [10, 25, 50, 75, 100]],
            'paging': true,
            'info': true,
            'filter': true,
            'stateSave': true,
            'processing': true,
            'serverSide': true,
            'ajax': {
                "url": base_url + "admin/customers/ajax_customers" + user_id,
                "type": "POST"
            },
            'columns': [
                { data: 'ci', 'sClass': 'text-center' },
                { 
                    'cell': true,
                    render: function (data, type, row) {
                    return `
                        ${row.first_name} ${row.last_name} 
                    `;
                        
                    }
                },
                {data: 'mobile'},
                {
                    'cell': true,
                    render: function (data, type, row) {
                        
                    return `<p title="${row.company}">
                        ${(row.company.length > 10)? row.company.substring(0, 10) + '...':row.company}
                    </p>`;  
                    }
                },
                {
                    'cell': true,
                    render: function (data, type, row) {
                        const statusVar = row.loan_status==1?'btn-outline-danger':'btn-outline-success';
                        const text = row.loan_status ==1?'Con crédito':'Sin crédito';
                    return `
                    <button type="button" class="btn btn-sm ${statusVar} status-check">
                        ${text}
                    </button>
                    `;  
                    }, sClass: 'text-center',
                },
                {
                    'cell': true,
                    render: function (data, type, row) {
                        const EDIT_URL = `${base_url}admin/customers/edit/${row.id}`;
                        const DELETE_URL = `${base_url}admin/customers/delete/${row.id}`;
                        if(CUSTOMER_UPDATE || (AUTHOR_CUSTOMER_UPDATE && SESSION_USER_ID == row.user_id))
                            btnUpdate = `<a class="btn btn-warning btn-circle btn-sm" href="${EDIT_URL}"><i class="fas fa-edit fa-sm" title="Editar"></i></a>`;
                        else
                            btnUpdate = '';
                        if(CUSTOMER_DELETE || (AUTHOR_CUSTOMER_DELETE && USER_ID == row.user_id))
                            btnDelete = `<button class="btn btn-danger btn-circle btn-sm" onClick="return deleteConfirmation('CONFIRMACIÓN', 'Se elimará el cliente (${row.first_name}) y sus préstamos.', '${DELETE_URL}')"><i class="fas fa-trash" title="Eliminar"></i></button>`;
                        else
                            btnDelete = '';
                    return `
                        ${btnUpdate}
                        ${btnDelete}
                    `;  
                    }, sClass: 'text-center'
                },
            ],
            "order": [[5, "asc"]]
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

