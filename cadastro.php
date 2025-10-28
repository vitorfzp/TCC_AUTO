<?php
// ... (seu PHP no topo) ...
?>  
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Prestador - Autonowe</title>
    <link rel="icon" type="image/png" href="img/logo_.png">
    <link rel="stylesheet" href="style/auth_style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
    <div class="auth-container">
        <div class="logo-section">
            <div class="logo-container">
            <img src="img/LOGO.png" alt="Logo Autonowe" class="logo-icon" />
                <h1>AUTONOWE</h1>
            </div>
        </div>
        <div class="form-section">
            <h2>Cadastro de Prestador</h2>
            <p style="text-align: center; margin-bottom: 20px;" id="form-description">Preencha para se tornar um prestador em nossa plataforma.</p>
            
            <div id="message-area" style="margin-bottom: 15px;"></div>

            <form class="auth-form" id="prestador-form" action="php/processar.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="nome">Nome Completo</label>
                    <input type="text" id="nome" name="nome" class="required-field" required>
                </div>
                <div class="form-group">
                    <label for="cpf">CPF</label>
                    <input type="text" id="cpf" name="cpf" class="required-field" required placeholder="Apenas números" maxlength="14">
                </div>
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="required-field" required>
                </div>
                
                <div class="form-group">
                    <label for="senha">Senha (mínimo 6 caracteres)</label>
                    <div class="password-wrapper">
                        <input type="password" id="senha" name="senha" class="required-field" required minlength="6">
                        <i class="fas fa-eye toggle-password" id="toggleSenha"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="confirma_senha">Confirmar Senha</label>
                    <div class="password-wrapper">
                        <input type="password" id="confirma_senha" name="confirma_senha" class="required-field" required minlength="6">
                        <i class="fas fa-eye toggle-password" id="toggleConfirma"></i>
                    </div>
                </div>
                <div class="form-group">
                    <label for="telefone">WhatsApp (Telefone)</label>
                    <input type="tel" id="telefone" name="telefone" class="required-field" required placeholder="(XX) XXXXX-XXXX">
                </div>
                <div class="form-group">
                    <label for="profissao">Área de Atuação</label>
                    <select id="profissao" name="profissao" class="required-field" required style="background-color: #f8fafc; border: 1px solid #dbeafe; border-radius: 12px; padding: 14px 18px; font-family: inherit; font-size: 1rem;">
                        <option value="" disabled selected>Selecione sua profissão</option>
                        <option value="Limpeza Geral">Limpeza Geral</option>
                        <option value="Pedreiro">Pedreiro</option>
                        <option value="Jardineiro">Jardineiro</option>
                        <option value="Segurança">Segurança</option>
                        <option value="Animador de Festa">Animador de Festa</option>
                        <option value="Barman">Barman</option>
                        <option value="Cabeleireiro">Cabeleireiro</option>
                        <option value="Transporte de aplicativo">Transporte de aplicativo</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="arquivo">Anexar currículo (Opcional)</label>
                    <input type="file" id="arquivo" name="arquivo">
                </div>
                <div class="form-group">
                    <label for="mensagem">Mensagem (Opcional)</label>
                    <textarea id="mensagem" name="mensagem" rows="3" style="background-color: #f8fafc; border: 1px solid #dbeafe; border-radius: 12px; padding: 14px 18px; font-family: inherit; font-size: 1rem;"></textarea>
                </div>
                
                <div class="form-group" style="flex-direction: row; align-items: center; gap: 10px;">
                    <input type="checkbox" id="terms" name="terms" style="width: auto;">
                    <label for="terms" style="margin-bottom: 0; font-weight: normal; font-size: 0.9rem;">
                        Eu li e aceito os <a href="termos.html" target="_blank">Termos de Serviço</a> e a <a href="termos.html" target="_blank">Política de Privacidade</a>.
                    </label>
                </div>

                <button type="submit" id="submitBtn" class="auth-button" disabled>Enviar Cadastro</button>
            </form>
             <p class="form-link">
                Já é um prestador? <a href="login.html">Faça Login</a>
             </p>
             <p class="form-link" style="margin-top: 10px;">
                <a href="index.php">← Voltar para o Início</a>
             </p>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            
            // --- CÓDIGO DE VALIDAÇÃO DO BOTÃO ---
            const submitBtn = document.getElementById('submitBtn');
            const termsCheckbox = document.getElementById('terms');
            const requiredFields = document.querySelectorAll('.required-field');
            const senhaInput = document.getElementById('senha');
            const confirmaSenhaInput = document.getElementById('confirma_senha');
            const messageArea = document.getElementById('message-area');

            function validateForm() {
                let allFieldsFilled = true;
                requiredFields.forEach(field => {
                    if (field.value.trim() === '') {
                        allFieldsFilled = false;
                    }
                });
                
                const termsAccepted = termsCheckbox.checked;
                const senhasCoincidem = senhaInput.value === confirmaSenhaInput.value;
                const senhaValida = senhaInput.value.length >= 6;

                // Mostra/esconde erro de senha em tempo real
                if (senhaInput.value.length > 0 && confirmaSenhaInput.value.length > 0) {
                    if (!senhasCoincidem) {
                        messageArea.innerHTML = `<p class="message error">As senhas não coincidem.</p>`;
                    } else if (!senhaValida) {
                         messageArea.innerHTML = `<p class="message error">A senha deve ter no mínimo 6 caracteres.</p>`;
                    } else {
                        messageArea.innerHTML = '';
                    }
                } else {
                     messageArea.innerHTML = '';
                }

                // Habilita o botão
                if (allFieldsFilled && termsAccepted && senhasCoincidem && senhaValida) {
                    submitBtn.disabled = false;
                } else {
                    submitBtn.disabled = true;
                }
            }

            requiredFields.forEach(field => {
                field.addEventListener('input', validateForm);
            });
            termsCheckbox.addEventListener('change', validateForm);
            
            // --- MÁSCARA DE CPF ---
            const cpfInput = document.getElementById('cpf');
            cpfInput.addEventListener('input', () => {
                let value = cpfInput.value.replace(/\D/g, '');
                value = value.substring(0, 11);
                let formattedValue = '';
                if (value.length > 9) {
                    formattedValue = `${value.substring(0, 3)}.${value.substring(3, 6)}.${value.substring(6, 9)}-${value.substring(9)}`;
                } else if (value.length > 6) {
                    formattedValue = `${value.substring(0, 3)}.${value.substring(3, 6)}.${value.substring(6)}`;
                } else if (value.length > 3) {
                    formattedValue = `${value.substring(0, 3)}.${value.substring(3)}`;
                } else {
                    formattedValue = value;
                }
                cpfInput.value = formattedValue;
                validateForm();
            });

            // --- SCRIPT DE SUCESSO/ERRO (da nossa conversa anterior) ---
            const params = new URLSearchParams(window.location.search);
            const formSection = document.querySelector('.form-section');

            if (params.has('success')) {
                const h2Title = formSection.querySelector('h2');
                const descriptionP = document.getElementById('form-description');
                const form = document.getElementById('prestador-form');
                const formLinks = formSection.querySelectorAll('.form-link');

                h2Title.textContent = 'Cadastro Recebido!';
                descriptionP.style.display = 'none';
                form.style.display = 'none';
                formLinks.forEach(link => link.style.display = 'none');

                messageArea.innerHTML = `
                    <div class="message success" style="padding: 1.5rem; text-align: center; border-radius: 12px; font-size: 1.1rem;">
                        <strong style="font-size: 1.5rem; display: block; margin-bottom: 10px; color: #155724;">Obrigado!</strong>
                        <p>Seu cadastro foi enviado com sucesso.</p>
                        <p>Agora você já pode fazer login com seu e-mail e senha.</p>
                        <br>
                        <a href="login.html" class="auth-button" style="text-decoration: none; display: inline-block; padding: 14px 25px;">Ir para o Login</a>
                    </div>
                `;
            } else if (params.has('error')) {
                messageArea.innerHTML = `<p class="message error">${decodeURIComponent(params.get('error'))}</p>`;
            }

            if (params.has('success') || params.has('error')) {
                window.history.replaceState({}, document.title, window.location.pathname);
            }

            // --- NOVO SCRIPT PARA VER SENHA ---
            function togglePasswordVisibility(toggleBtn, input) {
                toggleBtn.addEventListener('click', () => {
                    const type = input.getAttribute('type') === 'password' ? 'text' : 'password';
                    input.setAttribute('type', type);
                    toggleBtn.classList.toggle('fa-eye');
                    toggleBtn.classList.toggle('fa-eye-slash');
                });
            }
            togglePasswordVisibility(document.getElementById('toggleSenha'), senhaInput);
            togglePasswordVisibility(document.getElementById('toggleConfirma'), confirmaSenhaInput);
        });
    </script>
</body>
</html>