function handlerImagem(quillInstance) {
    const input = document.createElement('input');
    input.setAttribute('type', 'file');
    input.setAttribute('accept', 'image/*');
    input.click();

    input.onchange = async () => {
        const file = input.files[0];
        if (!file) return;

        const formData = new FormData();
        formData.append('image', file);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').getAttribute('content'));

        try {
            const response = await fetch(variaveis.rotaUploadImagem, {
                method: 'POST',
                body: formData,
            });

            if (!response.ok) throw new Error('Upload falhou');

            const data = await response.json();
            const range = quillInstance.getSelection(true);
            quillInstance.insertEmbed(range.index, 'image', data.url);
            quillInstance.setSelection(range.index + 1);
        } catch (error) {
            alert('Erro ao fazer upload da imagem. Tente novamente.');
        }
    };
}

const toolbarOptions = [
    ['bold', 'italic', 'underline', 'strike'],
    ['blockquote'],
    [{ 'list': 'ordered'}, { 'list': 'bullet' }],
    ['link', 'image'],
    ['clean']
];

const quill = new Quill('#editor', {
    theme: 'snow',
    modules: {
        toolbar: {
            container: toolbarOptions,
            handlers: {
                image: function() { handlerImagem(this.quill); }
            }
        }
    }
});

const quillEditar = new Quill('#editorEditar', {
    theme: 'snow',
    modules: {
        toolbar: {
            container: toolbarOptions,
            handlers: {
                image: function() { handlerImagem(this.quill); }
            }
        }
    }
});

const modalAdicionarProblema = document.getElementById('modalCriarProblema');
const botaoAdicionarProblema = document.getElementById('botaoAdicionarProblema');
const botaoCadastrarProblema = document.getElementById('botaoCadastrarProblema');

botaoAdicionarProblema.addEventListener('click', () => {
    modalAdicionarProblema.style.display = 'flex';
});

botaoCadastrarProblema.addEventListener('click',()=>{
    const checkboxes = document.querySelectorAll(".tarefaCheckbox");
    let tarefasSelecionadas = [];
    checkboxes.forEach(checkbox => {
      if (checkbox.checked) {
          tarefasSelecionadas.push(checkbox.dataset.id);
      }
  });
  const escrito = quill.getSemanticHTML();
  const titulo = document.getElementById('tituloProblemaCadastrar').value;

  const formulario = document.createElement('form');
  formulario.method = 'POST';
  formulario.action = variaveis.rotaAdicionar;
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const tokenInput = document.createElement('input');
  tokenInput.type = 'hidden';
  tokenInput.name = '_token';
  tokenInput.value = csrfToken;
  formulario.appendChild(tokenInput);

    const metodoPOST = document.createElement('input');
    metodoPOST.type = 'hidden';
    metodoPOST.name = '_method';
    metodoPOST.value = 'POST';
    formulario.appendChild(metodoPOST);

    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = "descricao";
    input.id = "enviarTarefa";
    input.value = escrito;
    formulario.appendChild(input);

    const input1 = document.createElement('input');
    input1.type = 'hidden';
    input1.name = "tarefas";
    input1.id = "enviarTarefa";
    input1.value = tarefasSelecionadas;
    formulario.appendChild(input1);

    const input3 = document.createElement('input');
    input3.type = 'hidden';
    input3.name = "idSessao";
    input3.id = "enviarTarefa";
    input3.value = variaveis.sessaoId;
    formulario.appendChild(input3);

    const input4 = document.createElement('input');
    input4.type = 'hidden';
    input4.name = "titulo";
    input4.id = "enviarTarefa";
    input4.value = titulo;
    formulario.appendChild(input4);

    document.body.appendChild(formulario);
    formulario.submit();
    document.body.removeChild(formulario);
})

const lixeiras = document.querySelectorAll('.lixeiraJaExiste');
const removerProblemaBotao = document.getElementById('removerProblemaBotao');

lixeiras.forEach(lixeira =>{
  lixeira.addEventListener('click',()=>{
    removerProblemaBotao.dataset.id = lixeira.dataset.id;
    document.getElementById('modalDeletarProblema').style.display = 'flex';
  });
})

removerProblemaBotao.addEventListener('click',()=>{
  const formulario = document.createElement('form');
  formulario.method = 'POST';
  formulario.action = variaveis.rotaDeletar;
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const tokenInput = document.createElement('input');
  tokenInput.type = 'hidden';
  tokenInput.name = '_token';
  tokenInput.value = csrfToken;
  formulario.appendChild(tokenInput);

  const metodoDELETE = document.createElement('input');
  metodoDELETE.type = 'hidden';
  metodoDELETE.name = '_method';
  metodoDELETE.value = 'DELETE';
  formulario.appendChild(metodoDELETE);

  const input = document.createElement('input');
  input.type = 'hidden';
  input.name = "id";
  input.value = removerProblemaBotao.dataset.id;
  formulario.appendChild(input);

  const input3 = document.createElement('input');
  input3.type = 'hidden';
  input3.name = "idSessao";
  input3.value = variaveis.sessaoId;
  formulario.appendChild(input3);

  document.body.appendChild(formulario);
  formulario.submit();
  document.body.removeChild(formulario);
});

const modalVisualizarProblema = document.getElementById('modalVisualizarProblema');
const botoesVisualizar = document.querySelectorAll('.olho');

botoesVisualizar.forEach(botao => {
  botao.addEventListener('click', () => {
    modalVisualizarProblema.style.display = 'flex';

    const problemaId = botao.dataset.id;
    const lugarDasTarefas = document.getElementById('tarefasNoModalVisualizar');
    lugarDasTarefas.innerHTML = '';

    for (let i = 0; i < variaveis.problemas.length; i++) {
      if (variaveis.problemas[i].id == problemaId) {
        document.getElementById('descricaoProblema').innerHTML = variaveis.problemas[i].descricao;

        if(variaveis.problemas[i].tarefas.length == 0){
          const divTarefa = document.createElement('div');
          divTarefa.classList.add('tarefaR');
          divTarefa.innerHTML = `<div class="tarefaTituloR">Nenhuma tarefa associada</div>`;
          lugarDasTarefas.appendChild(divTarefa);
        } else {
          variaveis.problemas[i].tarefas.forEach(tarefa => {
            const divTarefa = document.createElement('div');
            divTarefa.classList.add('tarefaR');
            divTarefa.innerHTML = `
               <div class="tarefaTituloR">${tarefa.titulo}</div>
                <div class="tarefaDescricaoR">${tarefa.descricao}</div>
            `;
            lugarDasTarefas.appendChild(divTarefa);
          });
        }

        break;
      }
    }
  });
});

const botaoEditar = document.querySelectorAll('.lapisJaNoBanco');

botaoEditar.forEach(botao => {
  botao.addEventListener('click', () => {
    document.getElementById('botaoEditarProblema').dataset.id = botao.dataset.id;
    const problemaId = botao.dataset.id;
    const problema = variaveis.problemas.find(problema => problema.id == problemaId);

    document.getElementById("tituloProblemaEditar").value = problema.titulo;
    quillEditar.root.innerHTML = problema.descricao;

    const checkboxes = document.querySelectorAll(".tarefaCheckboxEditar");
    checkboxes.forEach(checkbox => {
      checkbox.checked = false;
    });

    problema.tarefas.forEach(tarefa => {
      const checkbox = document.querySelector(`.tarefaCheckboxEditar[data-id="${tarefa.id}"]`);
      checkbox.checked = true;
    });

    modalEditarProblema.style.display = 'flex';
  });
});

const modalEditarProblema = document.getElementById('modalEditarProblema');

window.addEventListener('click', (event) => {
  if (event.target === modalAdicionarProblema) {
      modalAdicionarProblema.style.display = 'none';
  }
  if(event.target == modalVisualizarProblema){
    modalVisualizarProblema.style.display = 'none';
  }
  if(event.target == modalEditarProblema){
    modalEditarProblema.style.display = 'none';
  }
  if(event.target == document.getElementById('modalDeletarProblema')){
    document.getElementById('modalDeletarProblema').style.display = 'none';
  }
});

document.getElementById('botaoEditarProblema').addEventListener('click', () => {
  const checkboxes = document.querySelectorAll(".tarefaCheckboxEditar");
  let tarefasSelecionadas = [];
  checkboxes.forEach(checkbox => {
    if (checkbox.checked) {
        tarefasSelecionadas.push(checkbox.dataset.id);
    }
  });
  const escrito = quillEditar.getSemanticHTML();
  const formulario = document.createElement('form');
  formulario.method = 'POST';
  formulario.action = variaveis.rotaEditar;
  const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
  const tokenInput = document.createElement('input');
  tokenInput.type = 'hidden';
  tokenInput.name = '_token';
  tokenInput.value = csrfToken;
  formulario.appendChild(tokenInput);

  const metodoPUT = document.createElement('input');
  metodoPUT.type = 'hidden';
  metodoPUT.name = '_method';
  metodoPUT.value = 'PUT';
  formulario.appendChild(metodoPUT);

  const input = document.createElement('input');
  input.type = 'hidden';
  input.name = "descricao";
  input.value = escrito;
  formulario.appendChild(input);

  const input1 = document.createElement('input');
  input1.type = 'hidden';
  input1.name = "tarefas";
  input1.value = tarefasSelecionadas;
  formulario.appendChild(input1);

  const input2 = document.createElement('input');
  input2.type = 'hidden';
  input2.name = "id";
  input2.value = document.getElementById('botaoEditarProblema').dataset.id;
  formulario.appendChild(input2);

  const input3 = document.createElement('input');
  input3.type = 'hidden';
  input3.name = "idSessao";
  input3.value = variaveis.sessaoId;
  formulario.appendChild(input3);

  document.body.appendChild(formulario);
  formulario.submit();
  document.body.removeChild(formulario);
});

document.querySelector('.voltarDesistir').addEventListener('click', () => {
  document.getElementById('modalDeletarProblema').style.display = 'none';
});

document.querySelectorAll(".fecharVisualizar").forEach(botao =>{
  botao.addEventListener('click',()=>{
    modalVisualizarProblema.style.display = 'none';
    modalAdicionarProblema.style.display = 'none';
    modalEditarProblema.style.display = 'none';
    document.getElementById('modalDeletarProblema').style.display = 'none';
  });
});
