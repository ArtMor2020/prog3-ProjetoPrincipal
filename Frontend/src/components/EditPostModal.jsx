import React, { useState, useEffect } from 'react';
import { X } from 'lucide-react';
import MentionTextarea from './MentionTextarea';
import apiClient from '../api/axiosConfig';

export default function EditPostModal({ post, isOpen, onClose, onSave }) {
  const [title, setTitle] = useState('');
  const [description, setDescription] = useState('');

  useEffect(() => {
    if (post) {
      setTitle(post.title || '');
      setDescription(post.description || '');
    }
  }, [post]);

  if (!isOpen || !post) return null;

  const handleSave = () => {
    onSave(post.id, { title, description });
  };

  return (
    <div className="fixed inset-0 bg-black/60 flex items-center justify-center z-50">
      <div className="bg-white rounded-lg shadow-xl w-full max-w-lg p-6 relative">
        <button onClick={onClose} className="absolute top-4 right-4 text-gray-500 hover:text-gray-800"><X /></button>
        <h2 className="text-xl font-bold mb-4">Editar Post</h2>
        <div className="space-y-4">
          <div>
            <label className="block font-medium mb-1">Título</label>
            <input
              type="text"
              value={title}
              onChange={(e) => setTitle(e.target.value)}
              className="w-full border rounded px-3 py-2"
            />
          </div>
          <div>
            <label className="block font-medium mb-1">Descrição</label>
            <MentionTextarea
              value={description}
              onChange={setDescription}
              rows={8}
            />
          </div>
          <div className="flex justify-end space-x-3">
            <button onClick={onClose} className="px-4 py-2 bg-gray-200 rounded hover:bg-gray-300">Cancelar</button>
            <button onClick={handleSave} className="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Salvar Alterações</button>
          </div>
        </div>
      </div>
    </div>
  );
}