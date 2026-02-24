<template>
    <div class="flex flex-col h-full">
        <div class="flex items-center justify-between mb-3">
            <h2 class="text-gray-300 font-semibold text-sm uppercase tracking-wider">
                History
            </h2>
            <button
                v-if="calculations.length > 0"
                @click="$emit('clearAll')"
                class="text-xs text-red-400 hover:text-red-300 transition-colors"
            >
                Clear All
            </button>
        </div>

        <div class="flex-1 overflow-y-auto space-y-1 min-h-0" ref="listRef">
            <TransitionGroup name="tape">
                <div
                    v-for="calc in calculations"
                    :key="calc.id"
                    class="group flex items-center justify-between bg-gray-800/50 rounded-lg px-3 py-2 hover:bg-gray-800 transition-colors"
                >
                    <div class="min-w-0 flex-1 mr-2">
                        <div class="text-gray-400 text-xs font-mono truncate">
                            {{ calc.expression }}
                        </div>
                        <div class="text-white text-sm font-mono font-semibold">
                            = {{ formatResult(calc.result) }}
                        </div>
                    </div>
                    <button
                        @click="$emit('deleteOne', calc.id)"
                        class="text-gray-600 hover:text-red-400 opacity-0 group-hover:opacity-100 transition-all shrink-0"
                        title="Delete"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
            </TransitionGroup>

            <div
                v-if="calculations.length === 0"
                class="text-gray-600 text-sm text-center py-8"
            >
                No calculations yet
            </div>
        </div>
    </div>
</template>

<script setup>
defineProps({
    calculations: { type: Array, default: () => [] },
});

defineEmits(['deleteOne', 'clearAll']);

function formatResult(value) {
    const num = parseFloat(value);
    if (isNaN(num)) return value;
    if (Number.isInteger(num) && Math.abs(num) < 1e15) {
        return num.toLocaleString('en-US');
    }
    return num.toLocaleString('en-US', { maximumFractionDigits: 10 });
}
</script>

<style scoped>
.tape-enter-active {
    transition: all 0.3s ease;
}
.tape-leave-active {
    transition: all 0.2s ease;
}
.tape-enter-from {
    opacity: 0;
    transform: translateY(-10px);
}
.tape-leave-to {
    opacity: 0;
    transform: translateX(20px);
}
</style>
