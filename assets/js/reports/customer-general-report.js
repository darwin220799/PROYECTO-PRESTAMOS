// Cargar lista paginable
function loadData() {
    const entriesOptions = [10, 25, 50, 75, 100];
    $(document).ready(function () {
        if(document.getElementById('user_id'))
            user_id = document.getElementById('user_id').value != null?'/'+document.getElementById('user_id').value:'';
        else
            user_id = '';
        $("#customerGeneralReportTable").dataTable().fnDestroy();
        $('#customerGeneralReportTable').dataTable({
            "lengthMenu": [entriesOptions, entriesOptions],
            'paging': true,
            'info': true,
            'filter': true,
            'stateSave': true,
            'processing': true,
            'serverSide': true,
            'ajax': {
                "url": base_url + "admin/reports/ajax_report_by_customers" + user_id,
                "type": "POST"
            },
            'columns': [
                { data: 'id'},
                { data: 'ci'},
                { data: 'customer'},
                { 
                    'cell': true,
                    render: function (data, type, row) {
                        return `
                        <a href="${base_url}admin/reports/customer_pdf/${row.id}" target="_blank" class="btn btn-sm btn-secondary shadow-sm"><i class="fas fa-eye fa-sm"></i> Ver préstamos</a>
                        
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
