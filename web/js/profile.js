$(function () {
    $('#profile-tab-wrapper').on('beforeSubmit', '.create-form', function () {
        let form = $(this);
        let entityName = form.data('entityName');
        let formData = form.serializeArray();
        formData.push({name: 'was_submit', value: true});

        $.post({
            url: form.attr('action'),
            data: formData,
            success: function (answer) {
                if (answer.success) {
                    resetForm(form.attr('id'));
                    let entityGridId = `#${entityName}-list-grid-view`;
                    alert(`${entityName} was added successfully`);
                    $.pjax.reload({container: entityGridId});
                    $(entityGridId).on('click', '.btn-icon', handlerGridViewClick);
                    return false;
                }
                alert('Something went wrong. Please try again later.');
                return false;
            }
        });
        return false;
    });

    $('#profile-tab-wrapper').on('click', '.btn-icon', handlerGridViewClick);

    $('#transfers-tab-header').on('click', function () {
        let $transfersTabContent = $('#transfers-tab-content');

        if ($transfersTabContent.empty()) {
            $.post({
                url: '/transfer/get-tab-content',
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

    let data = {
        id: idEntity,
    };

    if ($(this).hasClass('replenish-btn')) {
        action = 'replenish';
        entityName = 'wallet';


        $.post({
            url: `/${entityName}/get-${action}-form`,
            data: data,
            success: function (content) {
                showModal(content);
            }
        });
    } else if ($(this).hasClass('cancel-btn') || $(this).hasClass('retry-btn')) {
        data.changeType = $(this).hasClass('cancel-btn') ? 'cancel' : 'retry';
        let isConfirm = confirm('Are you sure?');
        entityName = 'transfer';

        if (isConfirm) {
            $.post({
                url: `/${entityName}/change-status`,
                data: data,
                success: function (result) {
                    let message;

                    if (result.success) {
                        message = data.changeType === 'cancel' ?
                            'Transfer canceled successfully.' : 'Translation will be done at the end of this hour.';
                        let entityGridId = `#${entityName}-list-grid-view`;
                        $.pjax.reload({container: entityGridId});
                    } else {
                        message = 'Something went wrong. Please try again later.';
                    }
                    alert(message);
                }
            });
        }
    }
}