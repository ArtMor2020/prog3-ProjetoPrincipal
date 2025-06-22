import React from "react";
import { useState } from "react";
import axios from "axios";
import { useHistory } from "react-router-dom";

export default function Register() {
  const [form, setForm] = useState({
    name: "",
    email: "",
    password: "",
    about: "",
    is_private: false,
  });
  const [error, setError] = useState(null);
  const history = useHistory();

  const handleChange = (field, value) => {
    setForm((prev) => ({ ...prev, [field]: value }));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      const res = await axios.post("http://localhost:8080/users", form);
      console.log("Usuário criado com ID", res.data.id);
      history.push("/login");
    } catch (err) {
      console.error(err);
      setError(err.response?.data?.messages?.error || "Erro desconhecido");
    }
  };

  return (
    <div className="max-w-md mx-auto p-6 space-y-4">
      <h1 className="text-2xl font-bold text-center">Cadastrar-se</h1>
      <form onSubmit={handleSubmit} className="space-y-4">
        <input
          type="text"
          required
          placeholder="Nome"
          className="w-full p-2 border rounded"
          value={form.name}
          onChange={(e) => handleChange("name", e.target.value)}
        />
        <input
          type="email"
          required
          placeholder="Email"
          className="w-full p-2 border rounded"
          value={form.email}
          onChange={(e) => handleChange("email", e.target.value)}
        />
        <input
          type="password"
          required
          placeholder="Senha"
          className="w-full p-2 border rounded"
          value={form.password}
          onChange={(e) => handleChange("password", e.target.value)}
        />
        <textarea
          placeholder="Sobre você"
          className="w-full p-2 border rounded"
          value={form.about}
          onChange={(e) => handleChange("about", e.target.value)}
        />
        <label className="flex items-center space-x-2">
          <input
            type="checkbox"
            checked={form.is_private}
            onChange={(e) => handleChange("is_private", e.target.checked)}
          />
          <span>Conta privada?</span>
        </label>

        {error && <p className="text-red-600">{error}</p>}

        <button
          type="submit"
          className="w-full py-2 bg-blue-600 text-white rounded hover:bg-blue-700"
        >
          Criar conta
        </button>
      </form>
      <p className="text-center text-sm">
        Já tem conta?{" "}
        <button
          onClick={() => history.push("/login")}
          className="text-blue-600 underline"
        >
          Entrar
        </button>
      </p>
    </div>
  );
}
