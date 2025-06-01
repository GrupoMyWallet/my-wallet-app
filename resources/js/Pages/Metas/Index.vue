<script setup>
import { ref } from "vue";
import AppLayout from "@/Layouts/AppLayout.vue";
import TextInput from '@/Components/TextInput.vue'
import SelectInput from '@/Components/SelectInput.vue'
import InputError from '@/Components/InputError.vue'
import InputLabel from '@/Components/InputLabel.vue'
import Messages from '@/Components/Messages.vue'
import { useForm } from "@inertiajs/vue3";

defineProps({
    metas: Array,
});

const form = useForm({
    titulo: "",
    descricao: "",
    status: "andamento",
    valor_atual: 0,
    valor_a_alcancar: 0,
    data_final: "",
});

// Função para submit
function submit() {
    form.post("/metas", {
      onSuccess: () => form.reset(),
    });
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
                        v-mask-decimal
                        v-model="form.valor_a_alcancar" 
                        class="mt-1 block w-full" 
                        step="0.01"
                        placeholder="00.00"
                        required />
                    <InputError class="mt-2" :message="form.errors.valor_a_alcancar" />
                </div>

                <div>
                    <InputLabel for="valor_atual" value="Valor Atual" :required="true" />
                    <TextInput  
                        id="valor_atual" 
                        type="text" 
                        v-mask-decimal
                        v-model="form.valor_atual" 
                        class="mt-1 block w-full" 
                        step="0.01"
                        placeholder="00.00"
                        required />
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
    </AppLayout>
</template>
