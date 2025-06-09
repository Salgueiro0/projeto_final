// Versão de depuração do cep_script.js com console.log

// Adiciona um "ouvinte" ao campo de CEP
const cepInput = document.getElementById('cep');
if (cepInput) {
    cepInput.addEventListener('blur', function() {
        console.log('Checkpoint 1: Evento "blur" do CEP foi acionado.');

        const estadoSelect = document.getElementById('estado');
        const cidadeSelect = document.getElementById('cidade');
        const cepLoading = document.getElementById('cep-loading');
        const cep = this.value.replace(/\D/g, '');

        if (cep.length !== 8) {
            console.log('CEP inválido ou curto demais. Abortando.');
            return;
        }

        console.log('Checkpoint 2: CEP válido. Buscando na API:', cep);
        cepLoading.textContent = 'Buscando...';

        fetch(`https://viacep.com.br/ws/${cep}/json/`)
            .then(response => response.json())
            .then(data => {
                console.log('Checkpoint 3: Dados recebidos da API.', data);
                cepLoading.textContent = '';

                if (data.erro) {
                    alert('CEP não encontrado.');
                    return;
                }

                const uf = data.uf;
                let estadoEncontrado = false;
                for (const option of estadoSelect.options) {
                    if (option.getAttribute('data-uf') === uf) {
                        option.selected = true;
                        estadoEncontrado = true;
                        console.log('Checkpoint 4: Estado encontrado e selecionado no dropdown ->', uf);
                        estadoSelect.dispatchEvent(new Event('change'));
                        break;
                    }
                }
                
                if (!estadoEncontrado) {
                    alert('Estado retornado pelo CEP não está na nossa lista.');
                    return;
                }

                const nomeCidade = data.localidade;
                console.log('Checkpoint 5: Aguardando para selecionar a cidade:', nomeCidade);
                setTimeout(function() {
                    let cidadeEncontrada = false;
                    for (const option of cidadeSelect.options) {
                        if (option.text === nomeCidade) {
                            option.selected = true;
                            cidadeEncontrada = true;
                            console.log('Checkpoint 6: Cidade encontrada e selecionada!');
                            break;
                        }
                    }
                    if (!cidadeEncontrada) {
                         console.log('AVISO: A cidade retornada pelo CEP não foi encontrada no nosso dropdown.');
                    }
                }, 700); // Aumentei um pouco o tempo de espera
            })
            .catch(error => {
                console.error('ERRO FATAL na busca do CEP:', error);
                cepLoading.textContent = '';
                alert('Não foi possível buscar o CEP.');
            });
    });
}

// Script para carregar cidades quando o estado é mudado manualmente
const estadoSelectGlobal = document.getElementById('estado');
if (estadoSelectGlobal) {
    estadoSelectGlobal.addEventListener('change', function() {
        const estadoId = this.value;
        const cidadeSelect = document.getElementById('cidade');
        cidadeSelect.innerHTML = '<option value="">Carregando...</option>';
        cidadeSelect.disabled = true;

        if (estadoId) {
            fetch('buscar_cidades.php?estado_id=' + estadoId)
                .then(response => response.json())
                .then(cidades => {
                    cidadeSelect.innerHTML = '<option value="">-- Selecione uma Cidade --</option>';
                    cidades.forEach(cidade => {
                        const option = document.createElement('option');
                        option.value = cidade.id;
                        option.textContent = cidade.nome;
                        cidadeSelect.appendChild(option);
                    });
                    cidadeSelect.disabled = false;
                });
        } else {
            cidadeSelect.innerHTML = '<option value="">-- Escolha um estado primeiro --</option>';
        }
    });
}