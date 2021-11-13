function ajax_post(url, serialize, html) {
    $.ajax({
        url: url,
        type: (serialize === null) ? 'GET' : 'POST',
        data: serialize,
        beforeSend: function() {
            $('html, body').animate({scrollTop: 0}, 1500);
            html.html('<div class="alert alert-info">Carregando...</div>').hide().fadeIn('slow');
        },
        error: function (jqXHR) {
            if (jqXHR.status === 500) {
                html.html('<div class="alert alert-danger">Erro interno do servidor</div>').hide().fadeIn('slow');
            } else {
                let msg_error = jqXHR.responseJSON.messages;
                if (msg_error !== undefined) {
                    msg_error = msg_error.error;
                }
                if (msg_error !== undefined) {
                    html.html('<div class="alert alert-danger">' + msg_error + '</div>').hide().fadeIn('slow');
                } else {
                    html.html('<div class="alert alert-danger">Erro, tente novamente!</div>').hide().fadeIn('slow');
                }
            }
            return 0;
        },
        success: function(data) {
            html.hide().html(data).fadeIn('slow', function() {
                $('html, body').getNiceScroll().resize();
            });
            return 1;
        }
    });
}
