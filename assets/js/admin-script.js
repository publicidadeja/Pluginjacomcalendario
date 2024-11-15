jQuery(document).ready(function($) {
    // Configuração global do Media Uploader
    var mediaUploader;

    // Inicialização do Media Uploader com configurações melhoradas
    function initMediaUploader() {
        mediaUploader = wp.media({
            title: gmaData.wpMediaTitle || 'Selecione ou envie uma imagem',
            button: {
                text: gmaData.wpMediaButton || 'Usar esta imagem'
            },
            multiple: false,
            library: {
                type: 'image'
            }
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            updateImagePreview(attachment);
        });
    }

    // Função melhorada para atualizar preview de imagem
    function updateImagePreview(attachment) {
        $('#gma-imagem-url').val(attachment.url);
        $('#gma-arquivo-id').val(attachment.id);
        $('#gma-image-preview').html(`
            <div class="preview-container">
                <img src="${attachment.url}" alt="Pré-visualização da imagem" class="preview-image">
                <div class="image-info">
                    <span>Tamanho: ${attachment.width}x${attachment.height}px</span>
                    <span>Nome: ${attachment.filename}</span>
                </div>
                <button type="button" id="gma-remove-image" class="button">Remover imagem</button>
            </div>
        `);
        $('#gma-upload-btn').text('Alterar imagem');
        $('#gma-image-status')
            .text('Imagem selecionada: ' + attachment.filename)
            .attr('aria-live', 'polite')
            .addClass('success');
    }

    // Função para remover imagem
    function removeImage() {
        $('#gma-imagem-url, #gma-arquivo-id').val('');
        $('#gma-image-preview').empty();
        $('#gma-upload-btn').text('Selecionar imagem');
        $('#gma-image-status')
            .text('Nenhuma imagem selecionada')
            .attr('aria-live', 'polite')
            .removeClass('success');
    }

    // Eventos de clique melhorados
    $('#gma-upload-btn').on('click', function(e) {
        e.preventDefault();
        if (!mediaUploader) {
            initMediaUploader();
        }
        mediaUploader.open();
    });

    $(document).on('click', '#gma-remove-image', function(e) {
        e.preventDefault();
        removeImage();
    });

    // Validação de formulário aprimorada
    $('form.gma-form').on('submit', function(e) {
        var requiredFields = $(this).find('[required]');
        var hasErrors = false;

        requiredFields.each(function() {
            if (!$(this).val()) {
                hasErrors = true;
                $(this).addClass('error');
                showError($(this));
            } else {
                $(this).removeClass('error');
            }
        });

        if (hasErrors) {
            e.preventDefault();
            showNotice('Por favor, preencha todos os campos obrigatórios.', 'error');
            return false;
        }
    });

    // Função para mostrar notificações
    function showNotice(message, type = 'info') {
    // Criar elemento de áudio
    const audio = new Audio(gmaData.pluginUrl + '/assets/sounds/notification.mp3');
    
    // Criar popup estilizado
    const popup = $(`
        <div class="gma-popup fade-in ${type}">
            <div class="gma-popup-content">
                <div class="gma-popup-icon">
                    ${type === 'success' ? '✓' : type === 'error' ? '✕' : 'ℹ'}
                </div>
                <div class="gma-popup-message">${message}</div>
            </div>
        </div>
    `);

    // Adicionar ao corpo do documento
    $('body').append(popup);
    
    // Tocar som
    audio.play();
    
    // Remover após 5 segundos
    setTimeout(() => {
        popup.addClass('fade-out');
        setTimeout(() => popup.remove(), 300);
    }, 5000);
}
});
document.addEventListener('DOMContentLoaded', function() {
    // Manipulador de clique para thumbnails de vídeo
    document.querySelectorAll('.video-thumbnail').forEach(thumbnail => {
        thumbnail.addEventListener('click', function() {
            const videoUrl = this.dataset.videoUrl;
            const videoElement = document.createElement('video');
            videoElement.src = videoUrl;
            videoElement.controls = true;
            videoElement.autoplay = true;
            videoElement.className = 'material-video';
            
            this.parentElement.replaceChild(videoElement, this);
        });
    });
});

jQuery(document).ready(function($) {
    // Manipulador do formulário
    $('#gma-material-form').on('submit', function(e) {
        e.preventDefault();
        
        var formData = new FormData(this);
        formData.append('action', 'gma_criar_material');
        formData.append('nonce', gmaData.nonce);
        
        $.ajax({
            url: gmaData.ajaxurl,
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    window.location.href = response.data.redirect;
                } else {
                    alert('Erro ao criar material: ' + response.data.message);
                }
            },
            error: function() {
                alert('Erro ao enviar formulário');
            }
        });
    });

    // Inicialização do Media Uploader
    var mediaUploader;
    $('#gma-upload-btn').on('click', function(e) {
        e.preventDefault();
        
        if (mediaUploader) {
            mediaUploader.open();
            return;
        }

        mediaUploader = wp.media({
            title: gmaData.wpMediaTitle,
            button: {
                text: gmaData.wpMediaButton
            },
            multiple: false
        });

        mediaUploader.on('select', function() {
            var attachment = mediaUploader.state().get('selection').first().toJSON();
            $('#gma-imagem-url').val(attachment.url);
            $('#gma-arquivo-id').val(attachment.id);
            $('#gma-image-preview').html('<img src="' + attachment.url + '" alt="Preview">');
        });

        mediaUploader.open();
    });
});
