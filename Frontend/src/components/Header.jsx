// src/components/Header.jsx
import React, { useState, useRef, useEffect } from 'react';
import { Link, useHistory } from 'react-router-dom';
import { Search, Bell, MessageCircle, PlusSquare, X } from 'lucide-react';
import iconeProfile from '../images/iconeProfile.png';
import iconeLogo from '../images/logo.png';
import { useUser } from '../contexts/UserContext';

export default function Header({ onSearch }) {
  const { user, logout } = useUser();              // pega user e função de logout
  const [query, setQuery] = useState('');
  const [showModal, setShowModal] = useState(false);
  const [showMenu, setShowMenu]   = useState(false);
  const history = useHistory();
  const menuRef = useRef();

  const submit = () => {
    if (!query.trim()) return;
    onSearch(query);
    history.push(`/home?search=${encodeURIComponent(query)}`);
  };

  // Fecha o dropdown ao clicar fora
  useEffect(() => {
    const handleClickOutside = (e) => {
      if (menuRef.current && !menuRef.current.contains(e.target)) {
        setShowMenu(false);
      }
    };
    document.addEventListener('mousedown', handleClickOutside);
    return () => document.removeEventListener('mousedown', handleClickOutside);
  }, []);

  return (
    <>
      <header className="flex items-center px-4 py-2 bg-white shadow relative">
        <Link to="/home">
          <img src={iconeLogo} alt="Logo" className="h-8" />
        </Link>

        <div className="flex flex-1 mx-4">
          <input
            type="text"
            value={query}
            onChange={e => setQuery(e.target.value)}
            onKeyDown={e => e.key === 'Enter' && submit()}
            className="flex-1 border rounded-l px-3 py-2"
            placeholder="Buscar posts…"
          />
          <button
            onClick={submit}
            className="bg-blue-500 text-white px-4 rounded-r"
          >
            Enviar
          </button>
        </div>

        <nav className="flex items-center space-x-4">
          <MessageCircle className="w-6 h-6 cursor-pointer" />
          <PlusSquare
            className="w-6 h-6 cursor-pointer"
            onClick={() => setShowModal(true)}
          />
          <Bell className="w-6 h-6 cursor-pointer" />

          {/* Ícone de perfil com dropdown */}
          <div className="relative" ref={menuRef}>
            <img
              src={iconeProfile}
              alt="Perfil"
              className="w-8 h-8 rounded-full cursor-pointer"
              onClick={() => setShowMenu(prev => !prev)}
            />
            {showMenu && (
              <div className="absolute right-0 mt-2 w-40 bg-white border rounded shadow-lg z-50">
                <button
                  className="block w-full text-left px-4 py-2 hover:bg-gray-100"
                  onClick={() => {
                    setShowMenu(false);
                    history.push(`/users/${user.id}`);
                  }}
                >
                  Ver perfil
                </button>
                <button
                  className="block w-full text-left px-4 py-2 hover:bg-gray-100"
                  onClick={() => {
                    setShowMenu(false);
                    logout();               // limpa contexto / token
                    history.push('/login');
                  }}
                >
                  Sair
                </button>
              </div>
            )}
          </div>
        </nav>
      </header>

      {/* Modal de criação */}
      {showModal && (
        <div className="fixed inset-0 bg-black/50 flex items-center justify-center z-50">
          <div className="bg-white rounded-lg shadow-lg w-80 p-6 relative">
            {/* Botão fechar */}
            <button
              className="absolute top-3 right-3 text-gray-500 hover:text-gray-700"
              onClick={() => setShowModal(false)}
            >
              <X className="w-5 h-5" />
            </button>

            <h2 className="text-xl font-semibold mb-4">O que deseja criar?</h2>

            <div className="flex flex-col space-y-3">
              <button
                className="w-full px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-500"
                onClick={() => {
                  setShowModal(false);
                  history.push('/post/create');
                }}
              >
                Criar post
              </button>
              <button
                className="w-full px-4 py-2 bg-green-600 text-white rounded hover:bg-green-500"
                onClick={() => {
                  setShowModal(false);
                  history.push('/community/create');
                }}
              >
                Criar comunidade
              </button>
            </div>
          </div>
        </div>
      )}
    </>
  );
}
