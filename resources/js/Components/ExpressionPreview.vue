<template>
    <div
        v-if="expression.length > 0"
        class="font-mono text-lg leading-relaxed mt-2 px-1 break-all select-none"
        aria-hidden="true"
        v-html="colorized"
    />
</template>

<script setup>
import { computed } from 'vue';

const props = defineProps({
    expression: { type: String, default: '' },
});

const PAREN_COLORS = [
    'text-yellow-400',
    'text-pink-400',
    'text-cyan-400',
    'text-green-400',
    'text-purple-400',
    'text-orange-400',
];

const colorized = computed(() => {
    const expr = props.expression;
    let depth = 0;
    let html = '';
    // Track which depth each open paren was at, so close parens get the same color
    const depthStack = [];

    for (let i = 0; i < expr.length; i++) {
        const ch = expr[i];

        if (ch === '(') {
            const color = PAREN_COLORS[depth % PAREN_COLORS.length];
            depthStack.push(depth);
            depth++;
            html += `<span class="${color} font-bold">(</span>`;
        } else if (ch === ')') {
            if (depthStack.length > 0) {
                depth--;
                const openDepth = depthStack.pop();
                const color = PAREN_COLORS[openDepth % PAREN_COLORS.length];
                html += `<span class="${color} font-bold">)</span>`;
            } else {
                // Unmatched close paren — highlight as error
                html += `<span class="text-red-500 font-bold bg-red-500/20 rounded px-0.5">)</span>`;
            }
        } else {
            html += escapeHtml(ch);
        }
    }

    // If there are unclosed parens, we've already rendered them — that's fine,
    // the depth counter just won't return to 0. We could add a hint but the
    // visual nesting already communicates it.

    return html;
});

function escapeHtml(str) {
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;');
}
</script>
