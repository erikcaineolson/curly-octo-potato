import { describe, it, expect } from 'vitest';
import { mount } from '@vue/test-utils';
import ExpressionPreview from '../ExpressionPreview.vue';

describe('ExpressionPreview', () => {
    it('renders nothing when expression is empty', () => {
        const wrapper = mount(ExpressionPreview, {
            props: { expression: '' },
        });
        expect(wrapper.find('div').exists()).toBe(false);
    });

    it('renders plain characters without spans', () => {
        const wrapper = mount(ExpressionPreview, {
            props: { expression: '2+3' },
        });
        expect(wrapper.text()).toContain('2+3');
    });

    it('wraps parentheses in colored spans', () => {
        const wrapper = mount(ExpressionPreview, {
            props: { expression: '(1+2)' },
        });
        const spans = wrapper.findAll('span');
        // Opening and closing parens each get a span
        expect(spans.length).toBe(2);
        expect(spans[0].text()).toBe('(');
        expect(spans[1].text()).toBe(')');
    });

    it('colors nested parens at different depths', () => {
        const wrapper = mount(ExpressionPreview, {
            props: { expression: '((1))' },
        });
        const spans = wrapper.findAll('span');
        expect(spans.length).toBe(4);
        // Outer pair should have different color class than inner pair
        const outerClass = spans[0].attributes('class');
        const innerClass = spans[1].attributes('class');
        expect(outerClass).not.toBe(innerClass);
    });

    it('highlights unmatched close paren with error styling', () => {
        const wrapper = mount(ExpressionPreview, {
            props: { expression: '1+2)' },
        });
        const errorSpan = wrapper.find('.text-red-500');
        expect(errorSpan.exists()).toBe(true);
        expect(errorSpan.text()).toBe(')');
    });

    it('escapes HTML characters in the expression', () => {
        const wrapper = mount(ExpressionPreview, {
            props: { expression: '<script>' },
        });
        // Should not inject raw HTML
        expect(wrapper.html()).not.toContain('<script>');
        expect(wrapper.text()).toContain('<script>');
    });
});
