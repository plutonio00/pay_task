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
                    let $gridView = $(`#${entityName}-grid-view`);

                    alert(`${entityName} was added successfully`);

                    reloadPjaxOfGridView(entityName);
                    $($gridView).on('click', '.btn-icon', handlerGridViewClick);
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
        let $transfersWrapper = $('#transfers-wrapper');

        if ($transfersWrapper.is(':empty')) {
            $.post({
                url: '/transfer/get-user-transfers',
                success: function (html) {
                    $transfersWrapper.append(html);
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
            success: function (answer) {
                if (answer.success) {
                    $('#entity-actions-modal').modal('hide');
                    resetForm(form.attr('id'));
                    alert('Balance was replenished successfully');
                    let pjaxId = `#${entityName}-pjax-grid-view`;

                    reloadPjaxOfGridView(entityName, 'current');
                    $(pjaxId).on('click', '.btn-icon', handlerGridViewClick);
                    return false;
                }
                alert('Something went wrong. Please try again later.');
                return false;
            }
        });
        return false;
    });

});

function reloadPjaxOfGridView(entityName, pageType = 'last') {
    let pjaxId = `#${entityName}-pjax-grid-view`;
    let $gridView = $(`#${entityName}-grid-view`);
    let pageCount = $gridView.data('pageCount');

    if (pageType === 'last') {
        if (pageCount === 1) {
            $.pjax.reload({container: pjaxId, replace: false});
        }
        else {
            let paginationUrl = $gridView.data('paginationUrl');
            let paginationLinkLast = `${paginationUrl}?page=${pageCount}`;
            $.pjax.reload({container: pjaxId, url: paginationLinkLast, replace: false});
        }
    }
    else {
        let currentPage = $gridView.data('currentPage') + 1;
        let paginationUrl = $gridView.data('paginationUrl');
        let paginationLinkCurrent = `${paginationUrl}?page=${currentPage}`;
        $.pjax.reload({container: pjaxId, url: paginationLinkCurrent, replace: false});
    }
}

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

                        reloadPjaxOfGridView(entityName, 'current');
                    } else {
                        message = 'Something went wrong. Please try again later.';
                    }
                    alert(message);
                }
            });
        }
    }
}