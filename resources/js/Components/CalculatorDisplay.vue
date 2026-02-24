<template>
    <div class="bg-gray-900 rounded-xl p-4 font-mono">
        <div class="text-gray-400 text-right text-sm min-h-[1.5rem] truncate">
            {{ expression || '&nbsp;' }}
        </div>
        <div class="text-white text-right text-3xl font-bold min-h-[2.5rem] truncate">
            {{ display }}
        </div>
    </div>
</template>

<script setup>
import { computed } from 'vue';

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

function formatNumber(value) {
    const num = parseFloat(value);
    if (isNaN(num)) return value;
    if (Number.isInteger(num) && Math.abs(num) < 1e15) {
        return num.toLocaleString('en-US');
    }
    return num.toLocaleString('en-US', { maximumFractionDigits: 10 });
}
</script>
