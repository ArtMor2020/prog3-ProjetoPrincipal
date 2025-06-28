import React, { useEffect, useState } from "react";
import axios from "axios";
import { useHistory } from "react-router-dom";
import { useUser } from "../contexts/UserContext";
import Header from "../components/Header";
import MentionTextarea from "../components/MentionTextarea";
import { FileText, Archive, X } from "lucide-react";
import apiClient from '../api/axiosConfig';

const FilePreview = ({ file, onRemove }) => {
  const fileType = file.type.split("/")[0];
  const objectUrl = URL.createObjectURL(file);

  const renderPreview = () => {
    switch (fileType) {
      case "image":
        return (
          <img
            src={objectUrl}
            alt={file.name}
            className="h-full w-full object-cover rounded"
          />
        );
      case "video":
        return (
          <video
            src={objectUrl}
            className="h-full w-full object-cover rounded"
            controls
          />
        );
      default:
        return (
          <div className="flex flex-col items-center justify-center h-full bg-gray-100 rounded text-gray-500 p-1">
            {file.type.includes("zip") || file.type.includes("archive") ? (
              <Archive size={24} />
            ) : (
              <FileText size={24} />
            )}
            <span className="text-xs mt-1 break-all text-center">
              {file.name}
            </span>
          </div>
        );
    }
  };

  return (
    <div className="relative h-24 w-24 border rounded shadow-sm">
      {renderPreview()}
      <button
        type="button"
        onClick={() => onRemove(file)}
        className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-0.5 hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-red-400"
      >
        <X size={14} />
      </button>
    </div>
  );
};

export default function PostCreate() {
  const { user } = useUser();
  const history = useHistory();

  const [communities, setCommunities] = useState([]);
  const [form, setForm] = useState({
    id_community: "",
    title: "",
    description: "",
  });
  const [files, setFiles] = useState([]);
  const [error, setError] = useState("");

  useEffect(() => {
    if (!user) return;
    apiClient
      .get(`http://localhost:8080/user-communities/user/${user.id}`)
      .then((res) => {
        const memberOfCommunityIds = res.data.map((uc) =>
          String(uc.id_community)
        );
        if (memberOfCommunityIds.length > 0) {
          apiClient.get("http://localhost:8080/communities").then((allRes) => {
            const memberCommunities = allRes.data.filter((c) =>
              memberOfCommunityIds.includes(String(c.id))
            );
            setCommunities(memberCommunities);
          });
        }
      })
      .catch((err) => console.error("Erro ao carregar comunidades:", err));
  }, [user]);

  const handleChange = (name, value) => {
    setForm((prevForm) => ({ ...prevForm, [name]: value }));
  };

  const handleFileChange = (e) => {
    const newFiles = Array.from(e.target.files);
    setFiles((prevFiles) => [...prevFiles, ...newFiles]);
  };

  const handleRemoveFile = (fileToRemove) => {
    setFiles((prevFiles) => prevFiles.filter((file) => file !== fileToRemove));
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    setError("");

    if (!form.title.trim()) {
      setError("O Título é obrigatório.");
      return;
    }

    const formData = new FormData();
    formData.append("id_user", user.id);
    formData.append("title", form.title.trim());
    formData.append("description", form.description.trim());

    if (form.id_community && form.id_community !== "") {
      formData.append("id_community", form.id_community);
    }

    files.forEach((file) => {
      formData.append("attachments[]", file);
    });

    try {
      const res = await apiClient.post(
        "http://localhost:8080/posts/submit",
        formData,
        {
          headers: { "Content-Type": "multipart/form-data" },
        }
      );
      history.push(`/posts/${res.data.id}`);
    } catch (err) {
      console.error("Erro ao criar post:", err);
      setError(
        err.response?.data?.messages?.error || "Não foi possível criar o post."
      );
    }
  };

  return (
    <div>
      <Header onSearch={() => {}} />
      <div className="max-w-xl mx-auto mt-8 p-6 bg-white rounded-lg shadow mb-8">
        <h1 className="text-2xl font-semibold mb-4">Criar Novo Post</h1>
        {error && (
          <div className="mb-4 p-3 bg-red-100 text-red-800 rounded">
            {error}
          </div>
        )}

        <form onSubmit={handleSubmit} className="space-y-6">
          <div>
            <label className="block mb-1 font-medium">
              Escolha um local para postar
            </label>
            <select
              name="id_community"
              value={form.id_community}
              onChange={(e) => handleChange("id_community", e.target.value)}
              className="w-full border rounded px-3 py-2 bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <option value="">Meu Perfil (u/{user?.name})</option>
              {communities.map((c) => (
                <option key={c.id} value={c.id}>
                  r/{c.name}
                </option>
              ))}
            </select>
          </div>

          <div>
            <label className="block mb-1 font-medium">Título *</label>
            <input
              type="text"
              name="title"
              value={form.title}
              onChange={(e) => handleChange("title", e.target.value)}
              className="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
              placeholder="Um título interessante"
            />
          </div>

          <div>
            <label className="block mb-1 font-medium">
              Descrição (Opcional)
            </label>
            <MentionTextarea
              value={form.description}
              onChange={(newValue) => handleChange("description", newValue)}
              placeholder="Digite o conteúdo do post. Use u/username para mencionar."
              rows={6}
            />
          </div>

          <div>
            <label className="block mb-1 font-medium">Anexos</label>
            <div className="relative border-2 border-dashed rounded-lg p-6 text-center hover:border-blue-500 transition-colors">
              <input
                type="file"
                multiple
                onChange={handleFileChange}
                className="absolute inset-0 w-full h-full opacity-0 cursor-pointer"
                title="Clique ou arraste arquivos aqui"
              />
              <p className="text-gray-600">
                Arraste e solte arquivos aqui, ou{" "}
                <span className="text-blue-600 font-semibold">
                  clique para selecionar
                </span>
                .
              </p>
              <p className="text-xs text-gray-500 mt-1">
                Imagens, Vídeos, GIFs, Documentos, etc.
              </p>
            </div>
          </div>

          {files.length > 0 && (
            <div>
              <h3 className="text-sm font-medium mb-2">
                Arquivos Selecionados:
              </h3>
              <div className="grid grid-cols-3 sm:grid-cols-4 md:grid-cols-5 gap-4">
                {files.map((file, index) => (
                  <FilePreview
                    key={`${file.name}-${index}`}
                    file={file}
                    onRemove={handleRemoveFile}
                  />
                ))}
              </div>
            </div>
          )}

          <div className="text-right pt-4">
            <button
              type="submit"
              className="px-6 py-2 bg-blue-600 text-white font-semibold rounded hover:bg-blue-700 transition-all"
            >
              Publicar Post
            </button>
          </div>
        </form>
      </div>
    </div>
  );
}
