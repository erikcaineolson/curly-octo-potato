<template>
    <div class="grid grid-cols-4 gap-2">
        <button
            v-for="key in keys"
            :key="key.label"
            @click="handleKey(key)"
            :class="[
                'rounded-lg font-semibold text-lg py-3 transition-all duration-100',
                'active:scale-95 focus:outline-none focus:ring-2 focus:ring-blue-400',
                key.class,
            ]"
        >
            {{ key.label }}
        </button>
    </div>
</template>

<script setup>
const emit = defineEmits(['digit', 'operator', 'equals', 'clear', 'clearEntry', 'decimal']);

const keys = [
    { label: '7', type: 'digit', class: 'bg-gray-700 hover:bg-gray-600 text-white' },
    { label: '8', type: 'digit', class: 'bg-gray-700 hover:bg-gray-600 text-white' },
    { label: '9', type: 'digit', class: 'bg-gray-700 hover:bg-gray-600 text-white' },
    { label: '/', type: 'operator', value: 'divide', class: 'bg-amber-600 hover:bg-amber-500 text-white' },

    { label: '4', type: 'digit', class: 'bg-gray-700 hover:bg-gray-600 text-white' },
    { label: '5', type: 'digit', class: 'bg-gray-700 hover:bg-gray-600 text-white' },
    { label: '6', type: 'digit', class: 'bg-gray-700 hover:bg-gray-600 text-white' },
    { label: '\u00d7', type: 'operator', value: 'multiply', class: 'bg-amber-600 hover:bg-amber-500 text-white' },

    { label: '1', type: 'digit', class: 'bg-gray-700 hover:bg-gray-600 text-white' },
    { label: '2', type: 'digit', class: 'bg-gray-700 hover:bg-gray-600 text-white' },
    { label: '3', type: 'digit', class: 'bg-gray-700 hover:bg-gray-600 text-white' },
    { label: '\u2212', type: 'operator', value: 'subtract', class: 'bg-amber-600 hover:bg-amber-500 text-white' },

    { label: '0', type: 'digit', class: 'bg-gray-700 hover:bg-gray-600 text-white' },
    { label: '.', type: 'decimal', class: 'bg-gray-700 hover:bg-gray-600 text-white' },
    { label: '=', type: 'equals', class: 'bg-blue-600 hover:bg-blue-500 text-white' },
    { label: '+', type: 'operator', value: 'add', class: 'bg-amber-600 hover:bg-amber-500 text-white' },

    { label: 'C', type: 'clear', class: 'bg-red-700 hover:bg-red-600 text-white col-span-2' },
    { label: 'CE', type: 'clearEntry', class: 'bg-red-900 hover:bg-red-800 text-white col-span-2' },
];

function handleKey(key) {
    switch (key.type) {
        case 'digit':
            emit('digit', key.label);
            break;
        case 'decimal':
            emit('decimal');
            break;
        case 'operator':
            emit('operator', key.value);
            break;
        case 'equals':
            emit('equals');
            break;
        case 'clear':
            emit('clear');
            break;
        case 'clearEntry':
            emit('clearEntry');
            break;
    }
}
</script>
