$(function () {
    $('#profile-tab-wrapper').on('beforeSubmit', '.create-form', function () {
        let form = $(this);
        let entityName = form.data('entityName');
        let formData = form.serializeArray();
        formData.push({name: 'was_submit', value: true});

        $.post({
            url: form.attr('action'),
            data: formData,
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

    $('#transfers-tab-header').on('click', function () {
        let $transfersTabContent = $('#transfers-tab-content');
        let idUser = $(this).data('idUser');

        if ($transfersTabContent.empty()) {
            $.post({
                url: '/transfer/get-tab-content',
                data: { id_user: idUser },
                success: function (html) {
                    $transfersTabContent.append(html);
                }
            });
        }
    });

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