<script setup>
import { ref } from "vue";
import { useForm } from '@inertiajs/vue3';
import AppLayout from "@/Layouts/AppLayout.vue";
import Messages from '@/Components/Messages.vue';

defineProps({
    flash: Object
});

const statementFiles = ref([]);
const isDragOverStatement = ref(false);

const form = useForm({
    files: [],
    document_type: 'extrato'
});

const handleStatementFiles = (event) => {
    const files = Array.from(event.target.files);
    statementFiles.value = [...statementFiles.value, ...files];
};

const handleStatementDrop = (event) => {
    isDragOverStatement.value = false;
    const files = Array.from(event.dataTransfer.files);
    const validFiles = files.filter(
        (file) => 
            file.type.includes("csv") ||
            file.type.includes("pdf") ||
            file.name.endsWith(".csv") ||
            file.name.endsWith(".pdf")
    );
    statementFiles.value = [...statementFiles.value, ...validFiles];
};

const removeStatementFile = (index) => {
    statementFiles.value.splice(index, 1);
};

const formatFileSize = (bytes) => {
    if (bytes === 0) return "0 Bytes";
    const k = 1024;
    const sizes = ["Bytes", "KB", "MB", "GB"];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + " " + sizes[i];
};

const importStatements = () => {
    form.files = statementFiles.value;
    form.post(route('lancamentos.import.store'), {
        onSuccess: () => {
            statementFiles.value = [];
        }
    });
};
</script>

<template>
    <AppLayout title="Importar Lançamentos">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 dark:text-slate-200 leading-tight">
                Importar Lançamentos
            </h2>
        </template>
        
        <div class="bg-white dark:bg-slate-800 overflow-hidden shadow-xl sm:rounded-lg max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="p-4 lg:p-8">
                <!-- Header -->
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-slate-200">
                        Importar Extratos e Faturas
                    </h1>
                    <p class="mt-2 text-gray-600 dark:text-slate-400">
                        Importe seus lançamentos financeiros através de
                        extratos bancários e faturas de cartão
                    </p>
                </div>

                <!-- Resultado JSON -->
                <pre v-if="$page.props.flash.success" class="mb-6 bg-black text-green-400 p-4 rounded overflow-auto">{{ JSON.stringify($page.props.flash.success, null, 2) }}</pre>
                <pre v-if="$page.props.flash.error" class="mb-6 bg-black text-red-400 p-4 rounded overflow-auto">{{ JSON.stringify($page.props.flash.error, null, 2) }}</pre>

                <!-- Card de Importação -->
                <div class="bg-gray-50 dark:bg-slate-700 rounded-lg border border-gray-200 dark:border-slate-600 overflow-hidden mb-6">
                    <div class="p-4">
                        <div class="flex items-center mb-6">
                            <div class="flex-shrink-0">
                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <div class="ml-4">
                                <h3 class="text-xl font-medium text-gray-900 dark:text-slate-200">
                                    Importar Extratos e Faturas
                                </h3>
                                <p class="text-sm text-gray-500 dark:text-slate-400">
                                    Formatos suportados: PDF, CSV
                                </p>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <!-- Seletor de Tipo -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-slate-300 mb-2">
                                    Tipo de Documento
                                </label>
                                <select v-model="form.document_type" 
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-slate-600 rounded-md bg-white dark:bg-slate-800 text-gray-900 dark:text-slate-200">
                                    <option value="extrato">Extrato Bancário</option>
                                    <option value="fatura">Fatura de Cartão</option>
                                </select>
                            </div>

                            <!-- Upload Area -->
                            <div class="border-2 border-dashed border-gray-300 dark:border-slate-600 rounded-lg p-6 text-center hover:border-blue-400 dark:hover:border-blue-500 transition-colors cursor-pointer bg-white dark:bg-slate-800"
                                :class="{
                                    'border-blue-400 bg-blue-50 dark:bg-blue-900/20 dark:border-blue-500':
                                        isDragOverStatement,
                                }" 
                                @dragover.prevent="isDragOverStatement = true" 
                                @dragleave.prevent="isDragOverStatement = false" 
                                @drop.prevent="handleStatementDrop" 
                                @click="$refs.statementInput.click()">
                                <svg class="mx-auto h-16 w-16 text-gray-400 dark:text-slate-500" stroke="currentColor" fill="none"
                                    viewBox="0 0 48 48">
                                    <path
                                        d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02"
                                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                </svg>
                                <div class="mt-4">
                                    <p class="text-lg text-gray-600 dark:text-slate-400">
                                        <span class="font-medium text-blue-600 dark:text-blue-400">Clique para selecionar
                                            arquivos</span>
                                        ou arraste e solte aqui
                                    </p>
                                    <p class="text-sm text-gray-500 dark:text-slate-500 mt-2">
                                        PDF, CSV até 20MB por arquivo
                                    </p>
                                </div>
                            </div>

                            <input ref="statementInput" type="file" class="hidden" accept=".pdf,.csv" multiple
                                @change="handleStatementFiles" />

                            <!-- Lista de arquivos selecionados -->
                            <div v-if="statementFiles.length > 0" class="space-y-3">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-slate-300">
                                    Arquivos selecionados:
                                </h4>
                                <div class="space-y-2 max-h-48 overflow-y-auto">
                                    <div v-for="(file, index) in statementFiles" :key="index"
                                        class="flex items-center justify-between p-3 bg-white dark:bg-slate-700 rounded-lg border shadow-sm dark:border-slate-600">
                                        <div class="flex items-center min-w-0 flex-1">
                                            <div class="flex-shrink-0">
                                                <svg v-if="file.name.endsWith('.pdf')" 
                                                    class="h-6 w-6 text-red-600" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path d="M4 18h12V6l-4-4H4v16zm8-14v4h4l-4-4z" />
                                                </svg>
                                                <svg v-else class="h-6 w-6 text-green-600" fill="currentColor"
                                                    viewBox="0 0 20 20">
                                                    <path
                                                        d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" />
                                                </svg>
                                            </div>
                                            <div class="ml-3 min-w-0 flex-1">
                                                <p class="text-sm font-medium text-gray-900 dark:text-slate-200 truncate">
                                                    {{ file.name }}
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-slate-400">
                                                    {{ formatFileSize(file.size) }}
                                                </p>
                                            </div>
                                        </div>
                                        <button @click="removeStatementFile(index)" 
                                            class="ml-3 text-red-500 dark:text-red-400 hover:text-red-700 dark:hover:text-red-300 transition-colors">
                                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd"
                                                    d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                    clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Botão de Importar -->
                            <div class="flex justify-end">
                                <button 
                                    :disabled="statementFiles.length === 0 || form.processing"
                                    class="bg-blue-600 dark:bg-blue-700 text-white py-3 px-6 rounded-md hover:bg-blue-700 dark:hover:bg-blue-800 disabled:bg-gray-300 dark:disabled:bg-slate-600 disabled:cursor-not-allowed transition-colors font-medium"
                                    @click="importStatements">
                                    <span v-if="!form.processing">
                                        Importar {{ form.document_type === "extrato" ? "Extratos" : "Faturas" }}
                                    </span>
                                    <span v-else class="flex items-center">
                                        <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white"
                                            xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                            </path>
                                        </svg>
                                        Processando documentos...
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Seção de Ajuda -->
                <div class="bg-gray-50 dark:bg-slate-700 rounded-lg border border-gray-200 dark:border-slate-600 p-4">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-slate-200 mb-4 flex items-center">
                        <svg class="h-5 w-5 mr-2 text-gray-600 dark:text-slate-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Como importar seus documentos
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-3">
                            <h4 class="font-medium text-blue-600 dark:text-blue-400 text-base flex items-center">
                                <span class="mr-2">🏦</span> Extratos
                                Bancários e Faturas
                            </h4>
                            <ul class="text-sm text-gray-600 dark:text-slate-400 space-y-1 ml-6">
                                <li>
                                    • Arquivos PDF ou CSV do seu banco
                                </li>
                                <li>
                                    • Extratos de conta corrente ou
                                    poupança e Faturas
                                </li>
                                <li>
                                    • Principais bancos brasileiros
                                    suportados
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AppLayout>
</template>