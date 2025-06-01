<script setup lang="ts">
import { RouterLink, RouterView } from 'vue-router'
import { ref } from 'vue'
import { NButton, NTable, NCard, NModal } from 'naive-ui';
import { useBookActions } from './composables/useBookActions'
import HelloWorld from './components/HelloWorld.vue'
import BookModal from './components/BookModal.vue'
import DeleteBookConfirmation from './components/DeleteBookConfirmation.vue'

const { books, fetchBooks } = useBookActions()
const modalMode = ref(null)
const selectedBook = ref(null)
const isDeleteModalVisible = ref(false)

const openCreateModal = () => {
  modalMode.value = 'create'
  selectedBook.value = null
}

const openEditModal = (book) => {
  modalMode.value = 'edit'
  selectedBook.value = book
}

const openDeleteModal = (book) => {
  selectedBook.value = book
  isDeleteModalVisible.value = true
}

const closeModals = () => {
  isDeleteModalVisible.value = false
  modalMode.value = null
  selectedBook.value = null
  fetchBooks()
}

fetchBooks()
</script>

<template>
    <main v-if="books.length > 0">
      <div class="create-button-wrapper">
        <n-button secondary type="primary" size="large" @click="openCreateModal">
          New book
        </n-button>
      </div>
      <n-table :bordered="true" :single-line="false">
        <thead>
          <tr>
            <th>Title</th>
            <th>Author</th>
            <th></th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="book in books" :key="book.id">
            <td>{{ book.title }}</td>
            <td>{{ book.author }}</td>
            <td>
              <n-button size="small" @click="openEditModal(book)">Edit</n-button>
              <n-button type="error" dashed size="small" @click="openDeleteModal(book)">Delete</n-button>
            </td>
          </tr>
        </tbody>
      </n-table>
    </main>
    <BookModal
      v-if="modalMode !== null"
      :show="modalMode !== null"
      :mode="modalMode"
      :book="selectedBook"
      @close="closeModals"
    />
    <DeleteBookConfirmation
      :show="isDeleteModalVisible"
      :book="selectedBook"
      @close="closeModals"
    />
</template>

<style scoped>

main {
  min-height: 100dvh;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  margin-right:1rem;
  margin-left:1rem;

  .create-button-wrapper {
    margin: 1rem;
    width:100%;
    display:flex;
    justify-content: start;
  }

  table { 
    width:100%;

    thead {
      font-style: bold !important;
      th:last-child {
        width: 110px;
      }
    }
    tbody {
      td:last-child {
        display: flex;
        gap: 0.5rem;
      }
    }
  } 
}
</style>
