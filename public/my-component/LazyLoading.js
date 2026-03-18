let pageCache = {};
let currentPage = 1;
const pageSize = 10;

function loadPage(page) {
    if (pageCache[page]) {
        $("#grid")
            .jqGrid('clearGridData')
            .jqGrid('setGridParam', {
                datatype: 'local',
                data: pageCache[page]
            })
            .trigger('reloadGrid');
        return;
    }

    $("#grid").jqGrid('setGridParam', {
        datatype: 'json',
        page: page
    }).trigger('reloadGrid');
}
