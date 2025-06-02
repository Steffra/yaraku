import { describe, it, expect, vi } from 'vitest'
import { mount } from '@vue/test-utils'
import App from '../../App.vue'
import { NButton } from 'naive-ui'

vi.mock('../../composables/useBookActions', () => ({
    useBookActions: vi.fn(() => ({
        fetchBooks: vi.fn(),
        createBook: vi.fn(),
        editBook: vi.fn(),
        deleteBook: vi.fn(),
        exportBooks: vi.fn(),
        isSubmitting: false,
        books: [
            { id: 1, title: 'Book 1', author: 'Author 1' },
            { id: 2, title: 'Book 2', author: 'Author 2' },
        ],
    })),
}))

vi.mock('../../components/BookModal.vue', () => ({
    default: {
        template: '<div class="mock-book-modal">Mock Book Modal</div>',
    },
}))

vi.mock('../../components/ExportModal.vue', () => ({
    default: {
        template: '<div class="mock-export-modal">Mock Export Modal</div>',
    },
}))

describe('App.vue', () => {
    it('renders the table with books', () => {
        const wrapper = mount(App)
        const rows = wrapper.findAll('tbody tr')
        expect(rows).toHaveLength(2)
        expect(rows[0].text()).toContain('Book 1')
        expect(rows[0].text()).toContain('Author 1')
        expect(rows[1].text()).toContain('Book 2')
        expect(rows[1].text()).toContain('Author 2')
    })

    it('renders the "New book" button', () => {
        const wrapper = mount(App)
        const addButton = wrapper.findComponent(NButton)
        expect(addButton.exists()).toBe(true)
        expect(addButton.text()).toBe('New book')
    })

    it('opens the modal when "Add Book" button is clicked', async () => {
        const wrapper = mount(App)
        const addButton = wrapper.findComponent(NButton)
        await addButton.trigger('click')
        const modal = wrapper.find('.mock-book-modal')
        expect(modal.exists()).toBe(true)
        expect(modal.text()).toContain('Mock Book Modal')
    })

    it('opens the export modal when "Export" button is clicked', async () => {
        const wrapper = mount(App)
        const exportButton = wrapper.findAllComponents(NButton).at(1)
        await exportButton.trigger('click')
        const modal = wrapper.find('.mock-export-modal')
        expect(modal.exists()).toBe(true)
        expect(modal.text()).toContain('Mock Export Modal')
    })
})