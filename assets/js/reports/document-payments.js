function loadData() {
    $(document).ready(function () {
        const luserSelector = document.getElementById('userSelector');
        const user_id = luserSelector? '/' + document.getElementById('userSelector').value : '';
        $("#document-registers").dataTable().fnDestroy();
        $('#document-registers').dataTable({
            "lengthMenu": [[10, 25, 50, 75, 100], [10, 25, 50, 75, 100]],
            'paging': true,
            'info': true,
            'filter': true,
            'stateSave': true,
            'processing': true,
            'serverSide': true,
            'ajax': {
                "url": base_url + `admin/reports/ajax_document_payments/${user_id}`,
                "type": "POST"
            },
            'columns': [
                { data: 'id', sClass: 'text-center'},
                { data: 'customer_name' },
                { data: 'user_name' },
                { data: 'short_name' },
                { data: 'total_amount' },
                { data: 'pay_date' },
                {
                    'actions': true,
                    render: function (data, type, row) {
                        return `<a href="${base_url}admin/payments/document_payment/${row.id}" class="btn btn-success btn-sm" target="_blank">VER</a>`;
                    }, sClass: 'text-center'
                }
            ],
            "order": [[0, "desc"]]
        });
    });
}



if (document.getElementById("userSelector")) {
    const userSelector = document.getElementById('userSelector');
    userSelector.addEventListener('change', (event) => {
        loadData();
    });
}

loadData();