document.addEventListener('DOMContentLoaded', function() {
    const checkAll = document.getElementById('relatorio_completo');
    
    const childCheckboxes = document.querySelectorAll('.checkbox-conteudo');

    checkAll.addEventListener('change', function() {
        const isChecked = this.checked;

        childCheckboxes.forEach(function(checkbox) {
            checkbox.checked = isChecked;
        });
    });

    childCheckboxes.forEach(function(checkbox) {
        checkbox.addEventListener('change', function() {
            if (!this.checked) {
                checkAll.checked = false;
            } else {
                const allChecked = Array.from(childCheckboxes).every(function(cb) {
                    return cb.checked;
                });

                checkAll.checked = allChecked;
            }
        });
    });

});