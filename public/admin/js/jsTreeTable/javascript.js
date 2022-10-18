$(function () {
    var
        $table = $('#tree-table'),
        rows = $table.find('tr');

        $table2 = $('#tree-table2'),
        rows2 = $table2.find('tr');

    rows.each(function (index, row) {
        var
            $row = $(row),
            level = $row.data('level'),
            id = $row.data('id'),
            $columnName = $row.find('td[data-column="name"]'),
            children = $table.find('tr[data-parent="' + id + '"]');

        if (children.length) {
            var expander = $columnName.prepend('' +
                '<span class="treegrid-expander fas fa-caret-right"></span>' +
                '');

            children.hide();

            expander.on('click', function (e) {
                var $target = $(e.target);
                if(e.target.localName == 'span' && e.target.className.includes('treegrid-expander')){
                    if ($target.hasClass('fa-caret-right')) {
                        $target
                            .removeClass('fa-caret-right')
                            .addClass('fa-caret-down');
    
                        children.show();
                    } else {
                        $target
                            .removeClass('fa-caret-down')
                            .addClass('fa-caret-down');
    
                        reverseHide($table, $row);
                    }
                }
            });
        }

        $columnName.prepend('' +
            '<span class="treegrid-indent" style="width:' + 15 * level + 'px"></span>' +
            '');
    });

    rows2.each(function (index, row2) {
        var
            $row2 = $(row2),
            level = $row2.data('level'),
            id = $row2.data('id'),
            $columnName = $row2.find('td[data-column="name"]'),
            children = $table2.find('tr[data-parent="' + id + '"]');

        if (children.length) {
            var expander = $columnName.prepend('' +
                '<span class="treegrid-expander fas fa-caret-right"></span>' +
                '');

            children.hide();

            expander.on('click', function (e) {
                var $target = $(e.target);
                if(e.target.localName == 'span' && e.target.className.includes('treegrid-expander')){
                    if ($target.hasClass('fa-caret-right')) {
                        $target
                            .removeClass('fa-caret-right')
                            .addClass('fa-caret-down');

                        children.show();
                    } else {
                        $target
                            .removeClass('fa-caret-down')
                            .addClass('fa-caret-down');

                        reverseHide($table2, $row2);
                    }
                }
            });
        }

        $columnName.prepend('' +
            '<span class="treegrid-indent" style="width:' + 15 * level + 'px"></span>' +
            '');
    });

    // Reverse hide all elements
    reverseHide = function (table, element) {
        var
            $element = $(element),
            id = $element.data('id'),
            children = table.find('tr[data-parent="' + id + '"]');

        if (children.length) {
            children.each(function (i, e) {
                reverseHide(table, e);
            });

            $element
                .find('.fa-caret-down')
                .removeClass('fa-caret-down')
                .addClass('fa-caret-right');

            children.hide();
        }
    };
});
