var table;

$(document).ready(function () {

    ajaxcsrf();

    table = $("#detail_hasil").DataTable({
        initComplete: function () {
            var api = this.api();
            $('#detail_hasil_filter input')
                .off(".DT")
                .on("keypress.DT", function(e) {
                  if(e.which == 13) {
                    api.search(this.value).draw();
                    return false;
                  }
            });
        },
        lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
        oLanguage: {
            sProcessing: "loading..."
        },
        processing: true,
        serverSide: true,

        ajax: {
            "url": base_url + "hasilujian/ajax/nilai",
            data:{
                'id' : id
            },
            "type": "POST",
        },
        columns: [
            {
                "data": "id",
                "orderable": false,
                "searchable": false
            },
            { "data": 'nim' },
            { "data": 'nama' },
            { "data": 'detail_bobot_benar' },
            // { "data": 'jml_salah' },
            // { "data": 'nilai' },
            { "data": 'nilai_bobot_benar' },
            { "data": 'nilai' },
            {
                "data": 'aksi',
                "orderable": false,
                "searchable": false
            }
        ],
        order: [
            [4, 'desc'],
            [1, 'asc']
        ],
        rowId: function (a) {
            return a;
        },
        rowCallback: function (row, data, iDisplayIndex) {
            var info = this.fnPagingInfo();
            var page = info.iPage;
            var length = info.iLength;
            var index = page * length + (iDisplayIndex + 1);
            $('td:eq(0)', row).html(index);
        }
    });
});
