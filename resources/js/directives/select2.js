import { nextTick } from 'vue';
import $ from 'jquery';
import select2 from 'select2';
import 'select2/dist/css/select2.min.css';

select2(window, $);

// Tambahkan v-select2 pada select biasa. v-model tetap dipakai seperti sebelumnya.
export default {
    mounted(element) {
        const dropdown = $(element);

        dropdown.select2({
            width: '100%',
            placeholder: element.dataset.placeholder,
            language: {
                noResults: () => 'Data tidak ditemukan.',
            },
        });

        // Pilihan dari Select2 diteruskan ke v-model milik Vue.
        dropdown.on('select2:select.vueSelect2 select2:clear.vueSelect2', function () {
            element.dispatchEvent(new Event('change', { bubbles: true }));
        });
    },

    async updated(element) {
        // Tunggu Vue memperbarui value, pilihan, dan status disabled.
        await nextTick();

        const dropdown = $(element);

        if (dropdown.data('select2')) {
            // Vue dapat memperbarui class saat pesan validasi muncul.
            dropdown.addClass('select2-hidden-accessible');
            dropdown.trigger('change.select2');

            if (element.disabled) {
                dropdown.select2('close');
            }
        }
    },

    beforeUnmount(element) {
        // Bersihkan plugin saat berpindah halaman melalui Inertia.
        const dropdown = $(element);
        dropdown.off('.vueSelect2');

        if (dropdown.data('select2')) {
            dropdown.select2('destroy');
        }
    },
};
