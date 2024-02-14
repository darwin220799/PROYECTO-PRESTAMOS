// Cargar lista paginable
function loadData() {
    const entriesOptions = [10, 25, 50, 75, 100];
    $(document).ready(function () {
        if(document.getElementById('user_id'))
            user_id = document.getElementById('user_id').value != null?'/'+document.getElementById('user_id').value:'';
        else
            user_id = '';
        document.getElementById("week").href = base_url + 'admin/payments/quotes_week/' + document.getElementById('user_id').value;
        $("#loanItemsPayedTable").dataTable().fnDestroy();
        $('#loanItemsPayedTable').dataTable({
            "lengthMenu": [entriesOptions, entriesOptions],
            'paging': true,
            'info': true,
            'filter': true,
            'stateSave': true,
            'processing': true,
            'serverSide': true,
            'ajax': {
                "url": base_url + "admin/payments/ajax_payed_loan_items" + user_id,
                "type": "POST"
            },
            'columns': [
                { data: 'ci'},
                { data: 'name_cst'},
                { data: 'loan_id'},
                { data: 'num_quota'},
                { data: 'fee_amount'},
                { data: 'pay_date'},
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

