export default {
  beforeMount(el) {
    const handler = e => {
      // Só dígitos
      const digits = e.target.value.replace(/\D/g, '');
      // Converte para número decimal (2 casas)
      const num = parseFloat(digits || '0') / 100;
      // Formata no padrão 00.00 (com ponto)
      const newValue = num.toFixed(2);

      if (e.target.value !== newValue) {
        e.target.value = newValue;
        // ATENÇÃO: isso sincroniza com o v-model!
        e.target.dispatchEvent(new Event('input', { bubbles: true }));
      }
    };
    el.__maskDecimalHandler__ = handler;
    el.addEventListener('input', handler);
  },
  unmounted(el) {
    el.removeEventListener('input', el.__maskDecimalHandler__);
    delete el.__maskDecimalHandler__;
  }
};
