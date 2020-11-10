$(function () {
    $('.add-form').on('beforeSubmit', function () {
        let form = $(this);
        let data = form.serializeArray();
        let entityName = form.data('entityName');

        $.post({
            url: `/${entityName}/add`,
            data: data,
            success: function () {
                resetForm(form.attr('id'));
                $.pjax.reload({container: `#${entityName}-list-grid-view`});
                return false;
            }
        });
        return false;
    });
});

function resetForm(id) {
    $(`#${id}`).each(function () {
        this.reset();
    });
}