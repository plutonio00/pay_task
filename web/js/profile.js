$(function () {
    $('.create-form').on('beforeSubmit', function () {
        let form = $(this);
        let entityName = form.data('entityName');

        $.post({
            url: form.attr('action'),
            data: form.serializeArray(),
            success: function () {
                resetForm(form.attr('id'));
                let entityGridId = `#${entityName}-list-grid-view`;
                $.pjax.reload({container: entityGridId});
                $(entityGridId).on('click', '.btn-icon', handlerGridViewClick);
                return false;
            }
        });
        return false;
    });

    $('.entity-grid-view').on('click', '.btn-icon', handlerGridViewClick);

    // $('#profile-tab').on('click', function () {
    //
    // });

    $('#entity-actions-modal').on('beforeSubmit', 'form', function () {
        let form = $(this);
        let entityName = form.data('entityName');

        $.post({
            url: form.attr('action'),
            data: form.serializeArray(),
            success: function () {
                $('#entity-actions-modal').modal('hide');
                resetForm(form.attr('id'));
                let entityGridId = `#${entityName}-list-grid-view`;
                $.pjax.reload({container: entityGridId});
                $(entityGridId).on('click', '.btn-icon', handlerGridViewClick);
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

function showModal(content) {
    let idModal = '#entity-actions-modal';
    let modalWindow = $(idModal);
    let modalBody = modalWindow.find('.modal-body');
    modalBody.empty();
    modalBody.append(content);
    modalWindow.modal('show');
}



function handlerGridViewClick() {
    let idEntity = $(this).data('id');
    let action;
    let entityName;

    if ($(this).hasClass('replenish-btn')) {
        action = 'replenish';
        entityName = 'wallet';
    }

    let data = {
        id: idEntity,
    };

    $.post({
        url: `/${entityName}/get-${action}-form`,
        data: data,
        success: function (content) {
            showModal(content);
        }
    });
}