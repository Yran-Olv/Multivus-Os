<!-- Configurações do WhatsApp (Whaticket) - Arquivo Separado -->
<hr>
<h5 style="margin-left:10px;">Integração WhatsApp - Whaticket</h5>
<div class="control-group">
    <label for="whatsapp_enabled" class="control-label">Ativar Envio de OS via WhatsApp</label>
    <div class="controls">
        <select name="whatsapp_enabled" id="whatsapp_enabled">
            <option value="1" <?= (isset($configuration['whatsapp_enabled']) && $configuration['whatsapp_enabled'] == '1') ? 'selected' : ''; ?>>Ativar</option>
            <option value="0" <?= (!isset($configuration['whatsapp_enabled']) || $configuration['whatsapp_enabled'] == '0') ? 'selected' : ''; ?>>Desativar</option>
        </select>
        <span class="help-inline">Ativar ou desativar o envio de ordem de serviço em PDF via WhatsApp.</span>
    </div>
</div>
<div class="control-group">
    <label for="whatsapp_api_token" class="control-label">Token da API</label>
    <div class="controls">
        <input type="text" name="whatsapp_api_token" id="whatsapp_api_token" value="<?= isset($configuration['whatsapp_api_token']) ? $configuration['whatsapp_api_token'] : '' ?>" placeholder="Bearer token da API">
        <span class="help-inline">Token de autenticação da API do Whaticket. Acesse o menu 'Conexões', clique em editar e insira o token.</span>
    </div>
</div>
<div class="control-group">
    <label for="whatsapp_api_url" class="control-label">URL da API</label>
    <div class="controls">
        <input type="text" name="whatsapp_api_url" id="whatsapp_api_url" value="<?= isset($configuration['whatsapp_api_url']) && !empty($configuration['whatsapp_api_url']) ? $configuration['whatsapp_api_url'] : 'https://api.multivus.com.br/api/messages/send' ?>" placeholder="https://api.multivus.com.br/api/messages/send">
        <span class="help-inline">URL do endpoint da API do Whaticket para envio de mensagens.</span>
    </div>
</div>
<div class="control-group">
    <label for="whatsapp_send_signature" class="control-label">Assinar Mensagem</label>
    <div class="controls">
        <select name="whatsapp_send_signature" id="whatsapp_send_signature">
            <option value="1" <?= (!isset($configuration['whatsapp_send_signature']) || $configuration['whatsapp_send_signature'] == '1') ? 'selected' : ''; ?>>Sim</option>
            <option value="0" <?= (isset($configuration['whatsapp_send_signature']) && $configuration['whatsapp_send_signature'] == '0') ? 'selected' : ''; ?>>Não</option>
        </select>
        <span class="help-inline">Assinar a mensagem enviada via WhatsApp.</span>
    </div>
</div>
<div class="control-group">
    <label for="whatsapp_close_ticket" class="control-label">Encerrar Ticket</label>
    <div class="controls">
        <select name="whatsapp_close_ticket" id="whatsapp_close_ticket">
            <option value="1" <?= (isset($configuration['whatsapp_close_ticket']) && $configuration['whatsapp_close_ticket'] == '1') ? 'selected' : ''; ?>>Sim</option>
            <option value="0" <?= (!isset($configuration['whatsapp_close_ticket']) || $configuration['whatsapp_close_ticket'] == '0') ? 'selected' : ''; ?>>Não</option>
        </select>
        <span class="help-inline">Encerrar o ticket automaticamente após envio da mensagem.</span>
    </div>
</div>
