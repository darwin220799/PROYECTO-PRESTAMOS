// Cargar lista paginable
function loadData() {
    const entriesOptions = [10, 25, 50, 75, 100];
    $(document).ready(function () {
        if(document.getElementById('user_id'))
            user_id = document.getElementById('user_id').value != null?'/'+document.getElementById('user_id').value:'';
        else
            user_id = '';
        $("#loansTable").dataTable().fnDestroy();
        $('#loansTable').dataTable({
            "lengthMenu": [entriesOptions, entriesOptions],
            'paging': true,
            'info': true,
            'filter': true,
            'stateSave': true,
            'processing': true,
            'serverSide': true,
            'ajax': {
                "url": base_url + "admin/loans/ajax_loans" + user_id,
                "type": "POST"
            },
            'columns': [
                { data: 'id', 'sClass': 'text-center'},
                { data: 'customer'},
                { data: 'credit_amount'},
                { data: 'interest'},
                { data: 'total'},
                { data: 'coin_short_name'},
                { 
                    'cell': true,
                    render: function (data, type, row) {
                    return `
                        <button class="btn btn-sm ${row.status==1?'btn-outline-danger':'btn-outline-success'}">${row.status==1?'PENDIENTE':'PAGADO'}</button>
                    `;
                    }, 'sClass': 'text-center'
                },
                {
                    'cell': true,
                    render: function (data, type, row) {
                    return `
                        <a href="${base_url}admin/loans/view/${row.id}" class="btn btn-info btn-circle btn-sm" data-toggle="ajax-modal" title="Ver detalles"><i class="fas fa-info-circle"></i></a>
                    `;  
                    }, 'sClass': 'text-center'
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

