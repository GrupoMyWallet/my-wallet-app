<script setup>
import { ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import TextInput from '@/Components/TextInput.vue'
import SelectInput from '@/Components/SelectInput.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import Messages from '@/Components/Messages.vue'
import Modal from '@/Components/Modal.vue' // Assumindo que este é seu componente de modal
import { useForm } from "@inertiajs/vue3";

defineProps({
    metas: Array,
});

const form = useForm({
  titulo: "",
  descricao: "",
  status: "andamento",
  valor_atual: "",
  valor_a_alcancar: "",
  data_final: "",
});

const editForm = useForm({
  id: null,
  titulo: "",
  descricao: "",
  status: "andamento",
  valor_atual: "",
  valor_a_alcancar: "",
  data_final: "",
});

// Estados dos modais
const showEditModal = ref(false);
const showDeleteModal = ref(false);
const metaToDelete = ref(null);

function formatarReal(event, fieldName, formRef) {
  let valor = event.target.value.replace(/\D/g, ""); // remove tudo que não é dígito

  if (!valor) {
    event.target.value = "";
    formRef[fieldName] = "";
    return;
  }

  // Divide por 100 para centavos
  let valorNumerico = parseInt(valor) / 100;

  // Atualiza o form com valor numérico
  formRef[fieldName] = valorNumerico;

  // Formata para exibição (pt-BR)
  const valorFormatado = valorNumerico.toLocaleString('pt-BR', {
    minimumFractionDigits: 2,
    maximumFractionDigits: 2
  });

  event.target.value = `R$ ${valorFormatado}`;
}

// Função para submit de nova meta
function submit() {
    form.post("/metas", {
      onSuccess: () => form.reset(),
    });
}

// Função para abrir modal de edição
function openEditModal(meta) {
    editForm.id = meta.id;
    editForm.titulo = meta.titulo;
    editForm.descricao = meta.descricao;
    editForm.status = meta.status;
    editForm.valor_atual = meta.valor_atual;
    editForm.valor_a_alcancar = meta.valor_a_alcancar;
    editForm.data_final = meta.data_final;
    showEditModal.value = true;
}

// Função para fechar modal de edição
function closeEditModal() {
    showEditModal.value = false;
    editForm.reset();
}

// Função para salvar edição
function updateMeta() {
    editForm.put(`/metas/${editForm.id}`, {
        onSuccess: () => {
            closeEditModal();
        },
    });
}

// Função para abrir modal de exclusão
function openDeleteModal(meta) {
    metaToDelete.value = meta;
    showDeleteModal.value = true;
}

// Função para fechar modal de exclusão
function closeDeleteModal() {
    showDeleteModal.value = false;
    metaToDelete.value = null;
}

// Função para confirmar exclusão
function deleteMeta() {
    if (metaToDelete.value) {
        const deleteForm = useForm({});
        deleteForm.delete(`/metas/${metaToDelete.value.id}`, {
            onSuccess: () => {
                closeDeleteModal();
            },
        });
    }
}
</script>

<template>
    <AppLayout title="Metas">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Metas
            </h2>
        </template>

        <Messages />

        <div class="max-w-4xl mx-auto py-8 px-4 grid md:grid-cols-3 gap-10">
            <div class="md:col-span-2 space-y-4">
              <div class="bg-white rounded-xl shadow p-6">
                <h3 class="text-lg font-bold mb-3 flex items-center gap-2">
                  <span class="text-blue-600">Minhas metas</span>
                  <span class="text-xs bg-gray-100 text-gray-600 px-2 py-1 rounded">{{ metas.length }}</span>
                </h3>
                <ul class="divide-y divide-gray-200">
                  <li v-for="meta in metas" :key="meta.id" class="py-4">
                    <div class="flex justify-between items-center mb-2">
                      <div class="flex items-center gap-2">
                        <span :class="[
                          'w-2.5 h-2.5 rounded-full inline-block',
                          meta.status === 'completa'
                            ? 'bg-green-500'
                            : meta.status === 'cancelada'
                              ? 'bg-gray-400'
                              : 'bg-blue-500',
                        ]"></span>
                        <span class="font-medium">{{
                          meta.titulo
                        }}</span>
                        <span v-if="!meta.user_id"
                          class="text-xs bg-gray-200 text-gray-500 rounded px-2 py-0.5 ml-2">Geral</span>
                      </div>
                      <div class="flex items-center gap-2">
                        <span class="uppercase text-xs rounded px-2 py-0.5" :class="{
                          'bg-yellow-100 text-yellow-700':
                            meta.status === 'andamento',
                          'bg-green-100 text-green-700':
                            meta.status === 'completa',
                          'bg-gray-200 text-gray-600':
                            meta.status === 'cancelada',
                          'bg-blue-100 text-blue-700':
                            meta.status === 'pendente',
                        }">
                          {{ meta.status }}
                        </span>
                        <!-- Botões de ação -->
                        <div class="flex gap-1">
                          <button @click="openEditModal(meta)" 
                            class="text-blue-600 hover:text-blue-800 p-1 rounded">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                          </button>
                          <button @click="openDeleteModal(meta)" 
                            class="text-red-600 hover:text-red-800 p-1 rounded">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                          </button>
                        </div>
                      </div>
                    </div>
                    <div class="text-sm text-gray-600 mb-2">
                      {{ meta.descricao }}
                    </div>
                    <div class="flex items-center justify-between text-xs mb-1">
                      <span>R$
                        {{
                          Number(meta.valor_atual).toLocaleString(
                            "pt-BR",
                            { minimumFractionDigits: 2 }
                          )
                        }}
                        / R$
                        {{
                          Number(
                            meta.valor_a_alcancar
                          ).toLocaleString("pt-BR", {
                            minimumFractionDigits: 2,
                          })
                        }}</span>
                      <span v-if="meta.data_final" class="text-gray-400">até {{ meta.data_final }}</span>
                    </div>
                    <!-- Barra de progresso -->
                    <div class="w-full bg-gray-200 rounded-full h-2.5 mt-1">
                      <div class="h-2.5 rounded-full transition-all duration-500" :class="[
                        meta.status === 'completa'
                          ? 'bg-green-500'
                          : meta.status === 'cancelada'
                            ? 'bg-gray-400'
                            : 'bg-blue-500',
                      ]" :style="{
                        width:
                          Math.min(
                            100,
                            Math.round(
                              (meta.valor_atual /
                                meta.valor_a_alcancar) *
                              100
                            )
                          ) + '%',
                      }"></div>
                    </div>
                  </li>
                </ul>
              </div>
            </div>

          <!-- Formulário -->
            <div class="bg-white rounded-xl shadow p-6">
              <h3 class="font-bold mb-4 text-gray-700">Criar Nova meta</h3>
              <form @submit.prevent="submit" class="space-y-4">
                <div>
                    <InputLabel for="titulo" value="Titulo" :required="true" />
                    <TextInput  
                        id="titulo" 
                        type="text" 
                        v-model="form.titulo" 
                        class="mt-1 block w-full" 
                        step="0.01"
                        required />
                    <InputError class="mt-2" :message="form.errors.titulo" />
                </div>

                <div>
                    <InputLabel for="titulo" value="Descrição" :required="true" />
                    <textarea v-model="form.descricao" class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                      rows="2" required autocomplete="off"></textarea>
                    <InputError class="mt-2" :message="form.errors.descricao" />
                </div>

                <div>
                  <InputLabel for="valor_a_alcancar" value="Valor a Alcançar" :required="true" />
                  <TextInput  
                      id="valor_a_alcancar"
                      type="text"
                      :value="form.valor_a_alcancar ? `R$ ${form.valor_a_alcancar.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : ''"
                      class="mt-1 block w-full"
                      placeholder="R$ 0,00"
                      @input="formatarReal($event, 'valor_a_alcancar', form)"
                      required
                  />
                  <InputError class="mt-2" :message="form.errors.valor_a_alcancar" />
                </div>

                <div>
                <InputLabel for="valor_atual" value="Valor Atual" :required="true" />
                <TextInput  
                    id="valor_atual"
                    type="text"
                    :value="form.valor_atual ? `R$ ${form.valor_atual.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : ''"
                    class="mt-1 block w-full"
                    placeholder="R$ 0,00"
                    @input="formatarReal($event, 'valor_atual', form)"
                    required
                />
                <InputError class="mt-2" :message="form.errors.valor_atual" />
              </div>

                <div>
                  <InputLabel for="data_final" value="Data Final"/>
                    <TextInput  
                        id="data_final" 
                        type="text" 
                        v-maska="'##/##/####'"
                        v-model="form.data_final" 
                        class="mt-1 block w-full" 
                        placeholder="dia/mês/ano"
                    />
                    <InputError class="mt-2" :message="form.errors.data_final" />
                </div>

                <div>
                    <InputLabel for="status" value="Status" :required="true"/>
                    <SelectInput 
                        v-model="form.status"
                        id="status"
                        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                        required>
                        <option value="andamento">Em andamento</option>
                        <option value="cancelada">Cancelada</option>
                        <option value="completa">Completa</option>
                    </SelectInput>
                    <InputError class="mt-2" :message="form.errors.status" />
                </div>

                <button type="submit" :disabled="form.processing"
                  class="w-full bg-green-600 text-white py-2 rounded-lg hover:bg-green-700 transition">
                  Cadastrar meta
                </button>
              </form>
            </div>
        </div>

        <!-- Modal de Edição -->
        <Modal :show="showEditModal" @close="closeEditModal">
            <div class="p-6">
                <h3 class="text-lg font-bold mb-4">Editar Meta</h3>
                <form @submit.prevent="updateMeta" class="space-y-4">
                    <div>
                        <InputLabel for="edit_titulo" value="Titulo" :required="true" />
                        <TextInput  
                            id="edit_titulo" 
                            type="text" 
                            v-model="editForm.titulo" 
                            class="mt-1 block w-full" 
                            required />
                        <InputError class="mt-2" :message="editForm.errors.titulo" />
                    </div>

                    <div>
                        <InputLabel for="edit_descricao" value="Descrição" :required="true" />
                        <textarea v-model="editForm.descricao" 
                          class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                          rows="2" required autocomplete="off"></textarea>
                        <InputError class="mt-2" :message="editForm.errors.descricao" />
                    </div>

                    <div>
                        <InputLabel for="edit_valor_a_alcancar" value="Valor a Alcançar" :required="true" />
                        <TextInput  
                            id="edit_valor_a_alcancar" 
                            type="text" 
                            :value="editForm.valor_a_alcancar ? `R$ ${editForm.valor_a_alcancar.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : ''"
                            class="mt-1 block w-full" 
                            placeholder="R$ 0,00"
                            @input="formatarReal($event, 'valor_a_alcancar', editForm)"
                            required 
                        />
                        <InputError class="mt-2" :message="editForm.errors.valor_a_alcancar" />
                    </div>

                    <div>
                        <InputLabel for="edit_valor_atual" value="Valor Atual" :required="true" />
                        <TextInput  
                            id="edit_valor_atual" 
                            type="text" 
                            :value="editForm.valor_atual ? `R$ ${editForm.valor_atual.toLocaleString('pt-BR', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}` : ''"
                            class="mt-1 block w-full" 
                            placeholder="R$ 0,00"
                            @input="formatarReal($event, 'valor_atual', editForm)"
                            required 
                        />
                        <InputError class="mt-2" :message="editForm.errors.valor_atual" />
                    </div>

                    <div>
                        <InputLabel for="edit_data_final" value="Data Final"/>
                        <TextInput  
                            id="edit_data_final" 
                            type="text" 
                            v-maska="'##/##/####'"
                            v-model="editForm.data_final" 
                            class="mt-1 block w-full" 
                            placeholder="dia/mês/ano"
                        />
                        <InputError class="mt-2" :message="editForm.errors.data_final" />
                    </div>

                    <div>
                        <InputLabel for="edit_status" value="Status" :required="true"/>
                        <SelectInput 
                            v-model="editForm.status"
                            id="edit_status"
                            class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm mt-1 block w-full"
                            required>
                            <option value="andamento">Em andamento</option>
                            <option value="cancelada">Cancelada</option>
                            <option value="completa">Completa</option>
                        </SelectInput>
                        <InputError class="mt-2" :message="editForm.errors.status" />
                    </div>

                    <div class="flex justify-end gap-3 pt-4">
                        <button type="button" @click="closeEditModal"
                          class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                          Cancelar
                        </button>
                        <button type="submit" :disabled="editForm.processing"
                          class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">
                          Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </Modal>

        <!-- Modal de Confirmação de Exclusão -->
        <Modal :show="showDeleteModal" @close="closeDeleteModal">
            <div class="p-6">
                <h3 class="text-lg font-bold mb-4 text-red-600">Confirmar Exclusão</h3>
                <p class="text-gray-600 mb-6">
                    Tem certeza que deseja excluir a meta "<strong>{{ metaToDelete?.titulo }}</strong>"? 
                    Esta ação não pode ser desfeita.
                </p>
                <div class="flex justify-end gap-3">
                    <button type="button" @click="closeDeleteModal"
                      class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50">
                      Cancelar
                    </button>
                    <button type="button" @click="deleteMeta"
                      class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                      Excluir Meta
                    </button>
                </div>
            </div>
        </Modal>
    </AppLayout>
</template>