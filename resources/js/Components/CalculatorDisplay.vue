<template>
    <div class="bg-gray-900 rounded-xl p-4 font-mono">
        <div class="text-gray-400 text-right text-sm min-h-[1.5rem] truncate">
            {{ expression || '\u00A0' }}
        </div>
        <div class="text-white text-right text-3xl font-bold min-h-[2.5rem] truncate">
            {{ display }}
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';
import { formatNumber } from '../utils/formatNumber.js';

const props = defineProps({
    expression: { type: String, default: '' },
    currentValue: { type: String, default: '0' },
    result: { type: [String, null], default: null },
});

const display = computed(() => {
    if (props.result !== null) {
        return formatNumber(props.result);
    }
    return props.currentValue || '0';
});
</script>
