document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-table]').forEach((table) => {
        if (window.DataTable) {
            new DataTable(table, {
                pageLength: 25,
                language: {
                    search: 'ค้นหา:',
                    lengthMenu: 'แสดง _MENU_ รายการ',
                    info: 'แสดง _START_ ถึง _END_ จาก _TOTAL_ รายการ',
                    emptyTable: 'ยังไม่มีข้อมูล',
                    zeroRecords: 'ไม่พบข้อมูล',
                    paginate: { first: 'แรก', last: 'ท้าย', next: 'ถัดไป', previous: 'ก่อนหน้า' }
                }
            });
        }
    });
});

