<?php
if (!defined('ABSPATH')) exit;

// Carrega o Media Uploader
wp_enqueue_media();

// Localiza os scripts para AJAX
wp_localize_script('jquery', 'gma_ajax', array(
'ajaxurl' => admin_url('admin-ajax.php'),
'nonce' => wp_create_nonce('gma_copy_suggestions')
));
?>

<style>
:root {
    --primary-color: #4a90e2;
    --secondary-color: #2ecc71;
    --danger-color: #e74c3c;
    --text-color: #2c3e50;
    --background-color: #f5f6fa;
    --card-background: #ffffff;
    --border-radius: 10px;
    --transition: all 0.3s ease;
}

.gma-create-wrap {
    padding: 20px;
    background: var(--background-color);
    min-height: 100vh;
}

.gma-create-container {
    max-width: 800px;
    margin: 0 auto;
}

.gma-create-title {
    font-size: 2.5em;
    color: var(--text-color);
    text-align: center;
    margin-bottom: 30px;
    font-weight: 700;
}

.gma-create-card {
    background: var(--card-background);
    border-radius: var(--border-radius);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    padding: 30px;
    animation: slideIn 0.5s ease;
}

.gma-form-grid {
    display: grid;
    grid-template-columns: repeat(2, 1fr);
    gap: 20px;
}

.gma-form-group {
    margin-bottom: 20px;
}

.gma-form-group.full-width {
    grid-column: 1 / -1;
}

.gma-form-group label {
    display: flex;
    align-items: center;
    gap: 8px;
    margin-bottom: 8px;
    font-weight: 600;
    color: var(--text-color);
}

.gma-input, select, textarea {
    width: 100%;
    padding: 12px;
    border: 2px solid #e1e1e1;
    border-radius: var(--border-radius);
    font-size: 1em;
    transition: var(--transition);
}

.gma-input:focus, select:focus, textarea:focus {
    border-color: var(--primary-color);
    outline: none;
    box-shadow: 0 0 0 3px rgba(74, 144, 226, 0.2);
}

.gma-upload-container {
    display: flex;
    gap: 10px;
    margin-bottom: 15px;
}

.gma-image-preview {
    margin-top: 10px;
    max-width: 300px;
    border-radius: var(--border-radius);
    overflow: hidden;
}

.gma-image-preview img,
.gma-video-preview video {
    width: 100%;
    height: auto;
    display: block;
    border-radius: var(--border-radius);
}

.gma-image-preview-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
    margin-top: 10px;
}

.carrossel-item {
    position: relative;
}

.carrossel-image {
    width: 100%;
    height: auto;
    border-radius: var(--border-radius);
}

.gma-button {
    padding: 12px 24px;
    border: none;
    border-radius: var(--border-radius);
    cursor: pointer;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: var(--transition);
    text-decoration: none;
}

.gma-button.primary {
    background: var(--primary-color);
    color: white;
}

.gma-button.secondary {
    background: var(--secondary-color);
    color: white;
}

.gma-button:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.2);
}

.gma-form-actions {
    display: flex;
    gap: 15px;
    margin-top: 30px;
    justify-content: flex-end;
}

@keyframes slideIn {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

@media (max-width: 768px) {
    .gma-form-grid {
        grid-template-columns: 1fr;
    }
    
    .gma-form-actions {
        flex-direction: column;
    }
    
    .gma-button {
        width: 100%;
        justify-content: center;
    }
}

.error {
    border-color: var(--danger-color) !important;
}

.shake {
    animation: shake 0.5s linear;
}

@keyframes shake {
    0%, 100% { transform: translateX(0); }
    25% { transform: translateX(-10px); }
    75% { transform: translateX(10px); }
}

.midia-uploads-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 10px;
    margin-top: 10px;
}

.midia-item {
    position: relative;
}

.midia-item img {
    width: 100%;
    height: auto;
    border-radius: var(--border-radius);
}

.remove-midia {
    position: absolute;
    top: 10px;
    right: 10px;
    background: none;
    border: none;
    font-size: 1.2em;
    color: #e74c3c;
    cursor: pointer;
}

#suggestions-container {
    margin-top: 15px;
    border: 1px solid #ddd;
    padding: 15px;
    border-radius: var(--border-radius);
}

#suggestions-content {
    margin-top: 10px;
}
</style>

<div class="gma-create-wrap">
    <div class="gma-create-container">
        <h1 class="gma-create-title">🎨 Criar Novo Material</h1>
        <div class="gma-create-card">
            <form method="post" class="gma-create-form" id="gma-material-form">
                <?php wp_nonce_field('gma_novo_material', 'gma_novo_material_nonce'); ?>

<div class="gma-form-grid">
                <!-- Seleção de Campanha -->
                <div class="gma-form-group">
                    <label for="campanha_id">
                        <i class="dashicons dashicons-megaphone"></i> Campanha
                    </label>
                    <select name="campanha_id" id="campanha_id" required>
                        <option value="">Selecione uma campanha</option>
                        <?php 
                        $campanhas = gma_listar_campanhas();
                        foreach ($campanhas as $campanha): 
                            $tipo = esc_attr($campanha->tipo_campanha);
                        ?>
                            <option value="<?php echo esc_attr($campanha->id); ?>" 
                                    data-tipo="<?php echo $tipo; ?>">
                                <?php echo esc_html($campanha->nome); ?> 
                                (<?php echo ucfirst($tipo); ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Tipo de Mídia -->
                <div class="gma-form-group">
                    <label for="tipo_midia">
                        <i class="dashicons dashicons-format-gallery"></i> Tipo de Mídia
                    </label>
                    <select name="tipo_midia" id="tipo_midia" class="gma-input">
                        <option value="imagem">Imagem Única</option>
                        <option value="carrossel">Carrossel</option>
                        <option value="video">Vídeo</option>
                    </select>
                </div>

                <!-- Container de Imagem Única -->
                <div id="gma-image-container" class="gma-form-group">
                    <label for="gma-imagem-url">
                        <i class="dashicons dashicons-format-image"></i> Imagem
                    </label>
                    <div class="gma-upload-container">
                        <input type="text" name="imagem_url" id="gma-imagem-url" class="gma-input" readonly>
                        <input type="hidden" name="arquivo_id" id="gma-arquivo-id">
                        <button type="button" id="gma-upload-btn" class="gma-button secondary">
                            <i class="dashicons dashicons-upload"></i> Selecionar
                        </button>
                    </div>
                    <div id="gma-image-preview" class="gma-image-preview"></div>
                </div>

                <!-- Container de Carrossel -->
                <div id="carrossel-container" style="display: none;" class="gma-form-group">
                    <label>
                        <i class="dashicons dashicons-images-alt2"></i> Imagens do Carrossel
                    </label>
                    <div class="gma-upload-container">
                        <button type="button" id="add-carrossel-image" class="gma-button secondary">
                            <i class="dashicons dashicons-plus"></i> Adicionar Imagem
                        </button>
                    </div>
                    <div id="carrossel-preview" class="gma-image-preview-grid"></div>
                </div>

                <!-- Container de Vídeo -->
                <div id="video-container" style="display: none;" class="gma-form-group">
                    <label for="gma-video-url">
                        <i class="dashicons dashicons-video-alt3"></i> Vídeo
                    </label>
                    <div class="gma-upload-container">
                        <input type="text" name="video_url" id="gma-video-url" class="gma-input" readonly>
                        <button type="button" id="gma-upload-video-btn" class="gma-button secondary">
                            <i class="dashicons dashicons-upload"></i> Selecionar
                        </button>
                    </div>
                    <div id="gma-video-preview" class="gma-video-preview"></div>
                </div>

                <!-- Copy do Material -->
                <div class="gma-form-group full-width">
                    <label for="copy">
                        <i class="dashicons dashicons-editor-paste-text"></i> Copy
                    </label>
                    <textarea name="copy" id="copy" rows="5" required></textarea>
                    <div class="gma-character-count">
                        <span id="char-count">0</span> caracteres
                    </div>
                    <div class="gma-form-group full-width">
                        <button type="button" id="get-suggestions" class="gma-button secondary">
                            <i class="dashicons dashicons-admin-customizer"></i> Obter Sugestões AI
                        </button>
                        <div id="suggestions-container" style="display: none;">
                            <h3>Sugestões da IA</h3>
                            <div id="suggestions-content"></div>
                        </div>
                    </div>
                </div>

                <!-- Link do Canva -->
                <div class="gma-form-group full-width" id="canva-group" style="display: none;">
                    <label for="link_canva">
                        <i class="dashicons dashicons-art"></i> Link do Canva
                    </label>
                    <input type="url" name="link_canva" id="link_canva" 
                           class="gma-input" placeholder="https://www.canva.com/...">
                </div>
            </div>

            <div class="gma-form-actions">
                <button type="submit" name="criar_material" class="gma-button primary">
                    <i class="dashicons dashicons-saved"></i> Criar Material
                </button>
                <a href="<?php echo admin_url('admin.php?page=gma-materiais'); ?>" 
                   class="gma-button secondary">
                    <i class="dashicons dashicons-arrow-left-alt"></i> Voltar
                </a>
            </div>
        </form>
    </div>
</div>

<script>
jQuery(document).ready(function($) {
    // Controle de exibição dos campos baseado no tipo de campanha
    $('#campanha_id').on('change', function() {
        var selectedOption = $(this).find('option:selected');
        var tipoCampanha = selectedOption.data('tipo');
        
        if (tipoCampanha === 'marketing') {
            $('#canva-group').show();
        } else {
            $('#canva-group').hide();
        }
    });

    // Inicialização do Media Uploader para diferentes tipos de mídia
    function initializeMediaUploader(buttonId, inputId, previewId, multiple, mediaType) {
        $(buttonId).on('click', function(e) {
            e.preventDefault();
            
            var mediaUploader = wp.media({
                title: 'Selecionar Mídia',
                button: {
                    text: 'Usar esta mídia'
                },
                multiple: multiple,
                library: {
                    type: mediaType || 'image'
                }
            });

            mediaUploader.on('select', function() {
                if (multiple) {
                    var attachments = mediaUploader.state().get('selection');
                    attachments.each(function(attachment) {
                        var attachmentData = attachment.toJSON();
                        $(previewId).append(
                            '<div class="carrossel-item">' +
                            '<img src="' + attachmentData.url + '" alt="Preview" class="carrossel-image">' +
                            '<input type="hidden" name="carrossel_images[]" value="' + attachmentData.url + '">' +
                            '</div>'
                        );
                    });
                } else {
                    var attachment = mediaUploader.state().get('selection').first().toJSON();
                    if (inputId) $(inputId).val(attachment.url);
                    if (mediaType === 'video') {
                        $(previewId).html('<video src="' + attachment.url + '" controls></video>');
                    } else {
                        $(previewId).html('<img src="' + attachment.url + '" alt="Preview">');
                    }
                }
            });

            mediaUploader.open();
        });
    }

    // Inicializar uploaders para cada tipo de mídia
    initializeMediaUploader('#gma-upload-btn', '#gma-imagem-url', '#gma-image-preview', false);
    initializeMediaUploader('#add-carrossel-image', null, '#carrossel-preview', true);
    initializeMediaUploader('#gma-upload-video-btn', '#gma-video-url', '#gma-video-preview', false, 'video');

    // Controle de exibição dos campos de mídia
    $('#tipo_midia').on('change', function() {
        var selectedValue = $(this).val();
        
        if (selectedValue === 'video') {
            $('#carrossel-container').hide();
            $('#video-container').show();
            $('#gma-image-container').hide();
        } else if (selectedValue === 'carrossel') {
            $('#video-container').hide();
            $('#carrossel-container').show();
            $('#gma-image-container').hide();
        } else {
            $('#video-container').hide();
            $('#carrossel-container').hide();
            $('#gma-image-container').show();
        }
    });

    // Contador de caracteres
    $('#copy').on('input', function() {
        var charCount = $(this).val().length;
        $('#char-count').text(charCount);
    });

    // Validação do formulário
    $('#gma-material-form').on('submit', function(e) {
        var isValid = true;
        var tipoMidia = $('#tipo_midia').val();

        $('.error').removeClass('error');

        // Validação dos campos comuns
        if (!$('#campanha_id').val() || !$('#copy').val()) {
            isValid = false;
            if (!$('#campanha_id').val()) $('#campanha_id').addClass('error');
            if (!$('#copy').val()) $('#copy').addClass('error');
        }

        // Validação específica por tipo de mídia
        switch(tipoMidia) {
            case 'imagem':
                if (!$('#gma-imagem-url').val()) {
                    isValid = false;
                    $('#gma-imagem-url').addClass('error');
                }
                break;
            case 'carrossel':
                if ($('#carrossel-preview .carrossel-item').length === 0) {
                    isValid = false;
                    $('#carrossel-container').addClass('error');
                }
                break;
            case 'video':
                if (!$('#gma-video-url').val()) {
                    isValid = false;
                    $('#gma-video-url').addClass('error');
                }
                break;
        }

        if (!isValid) {
            e.preventDefault();
            alert('Por favor, preencha todos os campos obrigatórios.');
            return false;
        }
    });
});
</script>
