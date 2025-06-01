import { ref } from 'vue'

const BASE_URL = 'http://localhost:8080/api/books'

export function useBookActions() {
  const isSubmitting = ref(false)
  const books = ref([])

  const fetchBooks = async () => {
    try {
        const response = await fetch(BASE_URL)
        if (!response.ok) {
        throw new Error('Failed to fetch books')
      }
      books.value = await response.json()
    } catch (error) {
      console.error('Error fetching books:', error)
      throw error
    }
  }

  const createBook = async (title: string, author: string) => {
    isSubmitting.value = true
    try {
        const response = await fetch(BASE_URL, {
            method: 'POST',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ title, author }),
      })
      if (!response.ok) {
        throw new Error('Failed to create book')
      }
    } catch (error) {
      console.error('Error creating book:', error)
      throw error
    } finally {
      isSubmitting.value = false
    }
  }


  const editBook = async (id: string, title: string, author: string) => {
    isSubmitting.value = true
    try {
        const response = await fetch(`${BASE_URL}/${id}`, {
            method: 'PUT',
        headers: {
          'Content-Type': 'application/json',
        },
        body: JSON.stringify({ title, author }),
      })
      if (!response.ok) {
        throw new Error('Failed to update book')
      }
    } catch (error) {
      console.error('Error updating book:', error)
      throw error
    } finally {
      isSubmitting.value = false
    }
  }

  const deleteBook = async (id: string) => {
    isSubmitting.value = true
    try {
        const response = await fetch(`${BASE_URL}/${id}`, {
            method: 'DELETE',
      })
      if (!response.ok) {
        throw new Error('Failed to delete book')
      }
    } catch (error) {
      console.error('Error deleting book:', error)
      throw error
    } finally {
      isSubmitting.value = false
    }
  }

  return {
    isSubmitting,
    books,
    fetchBooks,
    createBook,
    editBook,
    deleteBook,
  }
}