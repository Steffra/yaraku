<script setup lang="ts">
import { ref, watch } from 'vue'
import { NButton, NTable, NInput,  } from 'naive-ui'
import { useBookActions } from './composables/useBookActions'
import BookModal from './components/BookModal.vue'
import DeleteBookConfirmation from './components/DeleteBookConfirmation.vue'
import ExportModal from './components/ExportModal.vue'

const { books, fetchBooks, } = useBookActions()
const modalMode = ref('')
const selectedBook = ref(null)
const isDeleteModalVisible = ref(false)
const isExportModalVisible = ref(false)

const titleFilter = ref('')
const authorFilter = ref('')
const sortBy = ref('')
const sortOrder = ref('asc')

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

const openExportModal = () => {
  isExportModalVisible.value = true
}

const closeModals = () => {
  isDeleteModalVisible.value = false
  modalMode.value = ''
  selectedBook.value = null
  isExportModalVisible.value = false
  document.body.focus()
  refreshList()
}

const refreshList = () => {
  fetchBooks(titleFilter.value, authorFilter.value, sortBy.value, sortOrder.value)
}

const toggleSort = (column) => {
  if (sortBy.value === column) {
    sortOrder.value = sortOrder.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortBy.value = column
    sortOrder.value = 'asc'
  }
}

watch([titleFilter, authorFilter, sortBy, sortOrder], () => {
  refreshList()
}, { immediate: true })

</script>

<template>
    <main >
      <div class="create-button-wrapper">
        <n-button secondary type="primary" size="large" @click="openCreateModal">
          New book
        </n-button>
        <n-button secondary type="default" size="large" @click="openExportModal">
          Export
        </n-button>
      </div>
      <n-table :bordered="true" :single-line="false">
        <thead>
        <tr>
          <th>
            <n-input v-model:value="titleFilter" placeholder="Title filter" :clearable="true" />
          </th>
          <th>
            <n-input v-model:value="authorFilter" placeholder="Author filter" :clearable="true"/>
          </th>
          <th></th>
        </tr>
        <tr>
          <th @click="toggleSort('title')" class="sortable">
            Title
            <span v-if="sortBy === 'title'">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
          </th>
          <th @click="toggleSort('author')" class="sortable">
            Author
            <span v-if="sortBy === 'author'">{{ sortOrder === 'asc' ? '▲' : '▼' }}</span>
          </th>
          <th></th>
        </tr>
      </thead>
        <tbody v-if="books.length > 0">
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
      :show="modalMode !== ''"
      :mode="modalMode"
      :book="selectedBook"
      @close="closeModals"
    />
    
    <DeleteBookConfirmation
      :show="isDeleteModalVisible"
      :book="selectedBook"
      @close="closeModals"
    />
    <ExportModal
      :show="isExportModalVisible"
      @close="closeModals"
    />
</template>

<style scoped>

main {
  display: flex;
  flex-direction: column;
  align-items: center;
  margin-right:1rem;
  margin-left:1rem;
  padding-top:10rem;

  .create-button-wrapper {
    margin: 1rem;
    width:100%;
    display:flex;
    justify-content: space-between;
  }

  table { 
    width:100%;

    thead {
      th {
        font-weight: bold; 
      }

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

.sortable {
  cursor: pointer;
  user-select: none;
  position: relative;
  text-align: left;
}

.sortable span {
  position: absolute;
  right: 15px;
  top: 50%;
  transform: translateY(-50%);
}
</style>
