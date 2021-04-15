var table;

$(document).ready(function() {
  ajaxcsrf();

  table = $("#hasil").DataTable({
    initComplete: function() {
      var api = this.api();
      $("#hasil_filter input")
        .off(".DT")
        .on("keyup.DT", function(e) {
          api.search(this.value).draw();
        });
    },
    lengthMenu: [[10, 25, 50, -1], [10, 25, 50, "All"]],
    dom:
      "<'row'<'col-lg-3'l><'col-lg-6 text-center'B><'col-lg-3'f>>" +
      "<'row'<'col-lg-12'tr>>" +
      "<'row'<'col-lg-5'i><'col-lg-7'p>>",
    buttons: [
      {
          text: '<i class="fa fa-refresh"></i> Reload',
          className: 'btn btn-secondary',
          action: function ( e, dt, node, config ) {
              reload_ajax();
          }
      },
    ],
    oLanguage: {
      sProcessing: "loading..."
    },
    processing: true,
    serverSide: true,
    ajax: {
      url: base_url + "hasilujian/ajax/data",
      type: "POST"
    },
    columns: [
      {
        data: "id_ujian",
        orderable: false,
        searchable: false
      },
      { data: "nama_ujian" },
      { data: "nama_matkul" },
      { data: "jumlah_soal" },
      { data: "waktu" },
      { data: "tgl_mulai" },
      {
        orderable: false,
        searchable: false
      }
    ],
    columnDefs: [
      {
        targets: 6,
        data: "id_ujian",
        render: function(data, type, row, meta) {
          return `
                    <div class="text-center">
                        <a class="btn btn-sm btn-info" href="${base_url}hasilujian/detail/${data}" >
                            <i class="fa fa-search"></i> Lihat
                        </a>
                    </div>
                    `;
        }
      }
    ],
    order: [[1, "asc"]],
    rowId: function(a) {
      return a;
    },
    rowCallback: function(row, data, iDisplayIndex) {
      var info = this.fnPagingInfo();
      var page = info.iPage;
      var length = info.iLength;
      var index = page * length + (iDisplayIndex + 1);
      $("td:eq(0)", row).html(index);
    }
  });
});
