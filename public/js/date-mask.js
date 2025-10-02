/**
 * Script para aplicar máscara brasileira em campos de data
 * Converte input[type="date"] para input[type="text"] com máscara DD/MM/AAAA
 */

document.addEventListener('DOMContentLoaded', function() {
    
    // Função para aplicar máscara de data
    function applyDateMask(input) {
        input.addEventListener('input', function(e) {
            let value = e.target.value.replace(/\D/g, ''); // Remove tudo que não é dígito
            
            if (value.length >= 2) {
                value = value.substring(0, 2) + '/' + value.substring(2);
            }
            if (value.length >= 5) {
                value = value.substring(0, 5) + '/' + value.substring(5, 9);
            }
            
            e.target.value = value;
        });
        
        // Validação de data
        input.addEventListener('blur', function(e) {
            const value = e.target.value;
            if (value && !isValidDate(value)) {
                e.target.classList.add('is-invalid');
                showDateError(e.target);
            } else {
                e.target.classList.remove('is-invalid');
                hideeDateError(e.target);
            }
        });
    }
    
    // Função para validar data no formato DD/MM/AAAA
    function isValidDate(dateString) {
        const regex = /^(\d{2})\/(\d{2})\/(\d{4})$/;
        const match = dateString.match(regex);
        
        if (!match) return false;
        
        const day = parseInt(match[1], 10);
        const month = parseInt(match[2], 10);
        const year = parseInt(match[3], 10);
        
        // Verifica se é uma data válida
        const date = new Date(year, month - 1, day);
        return date.getFullYear() === year && 
               date.getMonth() === month - 1 && 
               date.getDate() === day &&
               month >= 1 && month <= 12 &&
               day >= 1 && day <= 31;
    }
    
    // Função para mostrar erro de data
    function showDateError(input) {
        let errorDiv = input.parentNode.querySelector('.date-error');
        if (!errorDiv) {
            errorDiv = document.createElement('div');
            errorDiv.className = 'invalid-feedback date-error';
            errorDiv.textContent = 'Por favor, insira uma data válida no formato DD/MM/AAAA';
            input.parentNode.appendChild(errorDiv);
        }
        errorDiv.style.display = 'block';
    }
    
    // Função para esconder erro de data
    function hideeDateError(input) {
        const errorDiv = input.parentNode.querySelector('.date-error');
        if (errorDiv) {
            errorDiv.style.display = 'none';
        }
    }
    
    // Função para converter data do formato ISO (YYYY-MM-DD) para brasileiro (DD/MM/YYYY)
    function convertIsoToBrazilian(isoDate) {
        if (!isoDate) return '';
        const parts = isoDate.split('-');
        if (parts.length === 3) {
            return parts[2] + '/' + parts[1] + '/' + parts[0];
        }
        return isoDate;
    }
    
    // Função para converter data do formato brasileiro (DD/MM/YYYY) para ISO (YYYY-MM-DD)
    function convertBrazilianToIso(brazilianDate) {
        if (!brazilianDate) return '';
        const parts = brazilianDate.split('/');
        if (parts.length === 3) {
            return parts[2] + '-' + parts[1].padStart(2, '0') + '-' + parts[0].padStart(2, '0');
        }
        return brazilianDate;
    }
    
    // Função principal para converter campos de data
    function convertDateInputs() {
        const dateInputs = document.querySelectorAll('input[type="date"]');
        
        dateInputs.forEach(function(input) {
            // Salva o valor original se existir
            const originalValue = input.value;
            
            // Converte para input text
            input.type = 'text';
            input.setAttribute('placeholder', 'DD/MM/AAAA');
            input.setAttribute('maxlength', '10');
            input.setAttribute('pattern', '\\d{2}/\\d{2}/\\d{4}');
            input.setAttribute('title', 'Formato: DD/MM/AAAA');
            
            // Converte valor existente para formato brasileiro
            if (originalValue) {
                input.value = convertIsoToBrazilian(originalValue);
            }
            
            // Aplica máscara
            applyDateMask(input);
            
            // Adiciona campo hidden para enviar no formato ISO
            let hiddenInput = input.parentNode.querySelector('input[type="hidden"][name="' + input.name + '_iso"]');
            if (!hiddenInput) {
                hiddenInput = document.createElement('input');
                hiddenInput.type = 'hidden';
                hiddenInput.name = input.name + '_iso';
                input.parentNode.appendChild(hiddenInput);
            }
            
            // Atualiza campo hidden quando o valor muda
            input.addEventListener('input', function() {
                hiddenInput.value = convertBrazilianToIso(this.value);
            });
            
            // Define valor inicial do campo hidden
            if (originalValue) {
                hiddenInput.value = originalValue;
            }
        });
    }
    
    // Converte campos existentes
    convertDateInputs();
    
    // Observer para campos adicionados dinamicamente
    const observer = new MutationObserver(function(mutations) {
        mutations.forEach(function(mutation) {
            if (mutation.type === 'childList') {
                mutation.addedNodes.forEach(function(node) {
                    if (node.nodeType === 1) { // Element node
                        const dateInputs = node.querySelectorAll ? node.querySelectorAll('input[type="date"]') : [];
                        if (dateInputs.length > 0) {
                            // Pequeno delay para garantir que o DOM está pronto
                            setTimeout(convertDateInputs, 100);
                        }
                    }
                });
            }
        });
    });
    
    observer.observe(document.body, {
        childList: true,
        subtree: true
    });
});