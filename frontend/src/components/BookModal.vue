<script setup lang="ts">
import { ref, watch } from 'vue'
import { NButton, NInput, NForm, NFormItem, NCard, NModal } from 'naive-ui'
import { useBookActions } from '../composables/useBookActions'
import type { FormRules } from 'naive-ui'

const props = defineProps({
  show: Boolean,
  mode: {
    type: String,
    required: true,
  },
  book: {
    type: Object,
    default: null,
  },
})

const emit = defineEmits(['close'])

const { isSubmitting, createBook, editBook } = useBookActions()

const model = ref({ title: '', author:''})
const isModalVisible = ref(false)
const formRef = ref<InstanceType<typeof NForm> | null>(null)

const closeModal = () => {
  emit('close')
  model.value.title = ''
  model.value.author = ''
}

const handleSubmit = async () => {
  await formRef.value?.validate((errors) => {
    if(errors && errors.length > 0) {
      return
    }
  })

  try {
    if (props.mode === 'create') {
      await createBook(model.value.title, model.value.author)
    } else if (props.mode === 'edit' && props.book) {
      await editBook(props.book.id, model.value.title, model.value.author)
    }
    closeModal()
  } catch (error) {
    alert(`Failed to ${props.mode === 'create' ? 'add' : 'update'} book. Please try again.`)
  }
}

watch(
  () => props.show,
  (newValue) => {
    isModalVisible.value = newValue
    if (props.mode === 'edit' && props.book) {
      model.value.title = props.book.title || ''
      model.value.author = props.book.author || ''
    } else {
      model.value.title = ''
      model.value.author = ''
    }
  },
  { immediate: true }
)
watch(
  isModalVisible,
  (newValue) => {
    if (!newValue) {
      closeModal()
    }
  }
)

const rules: FormRules = {
      title: {
        required: true,
        trigger:['input', 'blur'],
        renderMessage: () => {
          return 'Title is required'
        }
      },
      author: {
        required: true,
        trigger:['input', 'blur'],
        renderMessage: () => {
          return 'Author is required'
        }
      }
    }
</script>

<template>
  <n-modal :preset="'dialog'" v-model:show="isModalVisible" @close="closeModal">
    <n-card :title="props.mode === 'create' ? 'Add New Book' : 'Edit Book'" :bordered="false">
      <n-form :model="model" :rules="rules" ref="formRef">
        <n-form-item label="Title" path="title">
          <n-input v-model:value="model.title" placeholder="Enter book title" :disabled="props.mode === 'edit'" :required="true" />
        </n-form-item>
        <n-form-item label="Author" path="author">
          <n-input v-model:value="model.author" placeholder="Enter book author" />
        </n-form-item>
        <div class="footer">
          <n-button type="tertiary" @click="closeModal">Cancel</n-button>
          <n-button secondary type="primary" :loading="isSubmitting" :disabled="isSubmitting" @click="handleSubmit">
            {{ props.mode === 'create' ? 'Submit' : 'Update' }}
          </n-button>
        </div>
      </n-form>
    </n-card>
  </n-modal>
</template>

<style>
n-card {
  padding: 20px;
}
.footer {
  display: flex;
  justify-content: space-between;
  gap: 1rem;
}
.n-dialog__icon {
  display: none;
}
</style>