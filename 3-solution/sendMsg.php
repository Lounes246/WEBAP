<?php
// Démarrer la session pour accéder aux informations de l'utilisateur
session_start();

// Vérifier si l'utilisateur est connecté en vérifiant l'existence de l'ID de session
if (!isset($_SESSION['id'])) {
  // S'il n'est pas connecté, rediriger vers la page de connexion
  header("Location: /3-solution/index.php");
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Pokémon Messaging</title>

  <!-- ✅ chemins absolus -->
  <link rel="stylesheet" href="/3-solution/css/main.css">
  <link href="https://fonts.cdnfonts.com/css/g-guarantee" rel="stylesheet">
  <script src="/3-solution/js/code.jquery.com_jquery-3.7.1.min.js"></script>

  <style>
    /* Conteneur principal pour l'ensemble de la mise en page de messagerie - flexbox pour les colonnes gauche/droite */
    .messaging-container { display:flex; height:calc(100vh - 150px); gap:20px; padding:20px; background:#f5f5f5; }

    /* Conteneur de la barre latérale pour les conversations et le chat global */
    .sidebar { width:320px; display:flex; flex-direction:column; gap:10px; }

    /* Styles pour les sections de la liste de conversations et de la barre latérale du chat global */
    .conversations-list, .global-chat-sidebar {
      background:white; border-radius:10px; padding:15px; overflow-y:auto;
      box-shadow:0 2px 4px rgba(0,0,0,0.1);
    }

    /* La liste de conversations prend l'espace disponible, le chat global a une hauteur fixe */
    .conversations-list { flex:1; }
    .global-chat-sidebar { height:150px; cursor:pointer; }

    /* Styles d'en-tête pour les deux sections */
    .conversations-list h3, .global-chat-sidebar h3 {
      margin:0 0 12px 0; color:#333; border-bottom:2px solid #ff6b6b; padding-bottom:10px;
    }

    /* Style d'état actif pour la barre latérale du chat global */
    .global-chat-sidebar.active {
      background:#fff3cd; border-left:4px solid #ffc107;
    }

    /* Changer la couleur de la bordure lorsque le chat global est actif */
    .global-chat-sidebar.active h3 {
      border-bottom-color:#ffc107;
    }

    /* Menu déroulant pour sélectionner un nouvel utilisateur à qui envoyer un message */
    .new-message-select {
      width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; margin-bottom:12px;
      background:#fff;
    }

    .conversation-item {
      padding:12px; margin-bottom:8px; background:#f9f9f9; border-left:4px solid #ddd;
      cursor:pointer; border-radius:6px; transition:all 0.2s ease;
    }
    /* Style lors du survol d'un élément de conversation */
    .conversation-item:hover { background:#f0f0f0; border-left-color:#ff6b6b; }
    /* Style pour la conversation actuellement actif/sélectionnée */
    .conversation-item.active { background:#fff3cd; border-left-color:#ffc107; font-weight:bold; }
    /* Style du nom d'utilisateur dans l'élément de conversation */
    .conversation-item .username { font-weight:bold; color:#333; }
    /* Aperçu du dernier message - tronquer avec ellipse s'il est trop long */
    .conversation-item .preview { font-size:12px; color:#999; margin-top:4px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    /* Badge affichant le nombre de messages non lus */
    .conversation-item .unread-badge {
      display:inline-flex; align-items:center; justify-content:center;
      background:#ff6b6b; color:white; border-radius:999px; min-width:22px; height:22px;
      font-size:12px; padding:0 7px; margin-left:6px;
    }
    /* Texte affiché quand il n'y a pas de conversations */
    .no-contacts { text-align:center; padding:18px; color:#999; }

    /* Conteneur principal de la zone de chat - flexbox pour empiler l'en-tête, les messages et l'entrée */
    .chat-area {
      flex:1; display:flex; flex-direction:column; background:white; border-radius:10px;
      box-shadow:0 2px 4px rgba(0,0,0,0.1); overflow:hidden;
    }
    /* Zone d'en-tête avec fond dégradé affichant le titre du chat */
    .chat-header {
      background:linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      color:white; padding:18px; border-bottom:2px solid #667eea;
    }
    /* Texte du titre dans l'en-tête du chat */
    .chat-header h2 { margin:0; font-size:20px; }

    /* Zone d'affichage des messages - conteneur défilable pour les messages de chat */
    .messages-display {
      flex:1; overflow-y:auto; padding:20px; display:flex; flex-direction:column; gap:14px;
      background:#fff;
    }
    /* Conteneur de message individuel - flex pour l'alignement */
    .message { display:flex; }
    /* Aligner les messages envoyés à droite */
    .message.sent { justify-content:flex-end; }
    /* Aligner les messages reçus à gauche */
    .message.received { justify-content:flex-start; }

    /* Style des bulles de message avec largeur maximale et retour à la ligne du texte */
    .message-bubble {
      max-width:60%; padding:12px 14px; border-radius:12px; word-wrap:break-word;
      box-shadow:0 1px 2px rgba(0,0,0,0.1);
    }
    /* Style des messages envoyés - fond bleu */
    .message.sent .message-bubble { background:#667eea; color:white; border-bottom-right-radius:5px; }
    /* Style des messages reçus - fond gris */
    .message.received .message-bubble { background:#e9ecef; color:#333; border-bottom-left-radius:5px; }

    /* Style d'horodatage pour les messages */
    .message-time { font-size:11px; color:#999; margin-top:5px; }
    /* Aligner l'horodatage à droite pour les messages envoyés */
    .message.sent .message-time { text-align:right; }
    /* Aligner l'horodatage à gauche pour les messages reçus */
    .message.received .message-time { text-align:left; }

    /* Conteneur de la zone d'entrée en bas - pour taper et envoyer des messages */
    .chat-input-area {
      padding:14px; border-top:1px solid #eee; display:flex; gap:10px; background:#fff;
    }
    /* Champ de saisie de texte pour taper des messages */
    .chat-input-area input {
      flex:1; padding:12px; border:1px solid #ddd; border-radius:999px; font-size:14px;
    }
    /* Style du bouton d'envoi */
    .chat-input-area button {
      padding:12px 20px; background:#667eea; color:white; border:none; border-radius:999px;
      cursor:pointer; font-weight:bold;
    }
    /* Style de l'état désactivé pour l'entrée et le bouton */
    .chat-input-area button:disabled, .chat-input-area input:disabled {
      opacity:0.6; cursor:not-allowed;
    }
    /* Message d'espace réservé quand aucune conversation n'est sélectionnée */
    .no-conversation {
      display:flex; align-items:center; justify-content:center; height:100%; color:#999; font-size:18px;
    }
    .chat-input-area textarea {
  flex: 1;
  resize: none;              /* pas de resize manuel */
  padding: 12px 16px;
  border: 1px solid #ddd;
  border-radius: 18px;
  font-size: 14px;
  line-height: 1.4;
  max-height: 120px;         /* ≈ 5 lignes */
  overflow-y: auto;
  font-family: inherit;
}

  </style>
</head>

<body>
<header>
  <img class="logo" src="/3-solution/img/logo.png" alt="Pokémon">
  <h1>Pokémon Messaging System</h1>
  <a href="/3-solution/team.php"
     style="position:absolute; right:20px; top:20px; color:white; text-decoration:none;">← Back to Team</a>
</header>

<main>
  <div class="messaging-container">

    <!-- LEFT: Sidebar with conversations + global chat -->
    <div class="sidebar">
      <div class="conversations-list">
        <h3>Direct Messages</h3>

        <select id="newUserSelect" class="new-message-select">
          <option value="">+ New message (choose a user)</option>
        </select>

        <div id="conversationsList"></div>
      </div>

      <div class="global-chat-sidebar" id="globalChatSidebar">
        <h3>🌍 Global Chat</h3>
        <p style="margin:0; font-size:12px; color:#999;">Click to join the public chat</p>
      </div>
    </div>

    <!-- RIGHT: Chat area -->
    <div class="chat-area">
      <div class="chat-header">
        <h2 id="chatTitle">Select a conversation to start chatting</h2>
      </div>

      <div class="messages-display" id="messagesContainer">
        <div class="no-conversation">No conversation selected</div>
      </div>

      <div class="chat-input-area">
  <textarea id="messageInput"
            placeholder="Type a message..."
            rows="1"
            disabled></textarea>
  <button id="sendMessageBtn" disabled>Send</button>
</div>

    </div>

  </div>
</main>

<script>
  // CONSTANTES
  const API = "/3-solution/php/";  // URL de base des points de terminaison de l'API

  // VARIABLES D'ÉTAT
  let currentConversationId = null;  // Stocke l'ID de la conversation directe actuellement sélectionnée
  let isGlobalChat = false;          // Drapeau pour suivre si l'utilisateur est dans le chat global ou la messagerie directe

  // Variables de protection contre la frappe
  let isTyping = false;              // Drapeau pour éviter les appels API excessifs pendant que l'utilisateur tape
  let typingTimer = null;            // ID de délai d'attente pour la détection de frappe

  // INITIALISATION - S'exécute au chargement de la page
  $(document).ready(function() {
    // Charger tous les utilisateurs pour le menu déroulant "nouveau message"
    loadUsers();
    // Charger les conversations existantes
    loadConversations();

    // INTERVALLES D'AUTO-ACTUALISATION
    // Actualiser la liste des conversations toutes les 2 secondes
    setInterval(loadConversations, 2000);

    // Actualiser le contenu des messages directs toutes les 2 secondes (quand pas en train de taper, pas dans le chat global)
    setInterval(function(){
      if (currentConversationId && !isTyping && !isGlobalChat) {
        loadMessages(currentConversationId);
      }
    }, 2000);

    // Actualiser le chat global toutes les 2 secondes (quand pas en train de taper, dans le chat global)
    setInterval(function(){
      if (isGlobalChat && !isTyping) {
        loadGlobalChat();
      }
    }, 2000);

    // ENVOI DE MESSAGES - Clic et Touche Entrée
    // Envoyer un message quand le bouton Envoyer est cliqué
    $("#sendMessageBtn").on("click", function() {
      if (isGlobalChat) {
        sendGlobalMessage();
      } else {
        sendMessage();
      }
    });

    // Envoyer un message quand la touche Entrée est enfoncée
    $("#messageInput").on("keypress", function(e) {
      if (e.which === 13) {  // 13 est le code de la touche Entrée
        if (isGlobalChat) {
          sendGlobalMessage();
        } else {
          sendMessage();
        }
        return false;  // Empêcher la soumission de formulaire par défaut
      }
    });

    // PROTECTION CONTRE LA FRAPPE - Éviter les appels API excessifs
    $("#messageInput").on("input", function(){
      isTyping = true;
      clearTimeout(typingTimer);
      // Marquer l'utilisateur comme non en train de taper après 1,2 secondes sans saisie
      typingTimer = setTimeout(() => { isTyping = false; }, 1200);
    }).on("focus", function(){
      // L'utilisateur tape quand focalisé
      isTyping = true;
    }).on("blur", function(){
      // L'utilisateur a arrêté de taper quand le focus est perdu
      isTyping = false;
      clearTimeout(typingTimer);
    });

    // SÉLECTION DE L'UTILISATEUR - Nouveau message direct
    // Quand l'utilisateur sélectionne quelqu'un du menu déroulant "nouveau message"
    $("#newUserSelect").on("change", function() {
      const id = parseInt($(this).val(), 10);
      if (!id) return;

      const username = $("#newUserSelect option:selected").text();
      openConversation(id, username);

      // Réinitialiser le menu déroulant à l'option par défaut
      $(this).val("");
    });

    // SÉLECTION DE CONVERSATION - Cliquer sur une conversation existante
    // Quand l'utilisateur clique sur une conversation existante dans la liste
    $(document).on("click", ".conversation-item", function() {
      const id = parseInt($(this).attr("data-trainer-id"), 10);
      const username = $(this).find(".username").text();
      openConversation(id, username);
    });

    // SÉLECTION DU CHAT GLOBAL
    // Quand l'utilisateur clique sur la barre latérale du chat global
    $("#globalChatSidebar").on("click", function() {
      console.log('globalChatSidebar clicked');
      openGlobalChat();
    });
  });

  // FONCTION: Charger tous les utilisateurs pour le menu déroulant
  function loadUsers() {
    $.ajax({
      url: API + "getUsers.php",
      method: "GET",
      dataType: "json",
      timeout: 8000,
      success: function(res) {
        if (!res || !res.success) return;

        // Construire les options du menu déroulant avec tous les utilisateurs
        let options = `<option value="">+ Nouveau message (choisir un utilisateur)</option>`;
        res.users.forEach(u => {
          options += `<option value="${u.idTrainer}">${escapeHtml(u.username)}</option>`;
        });
        $("#newUserSelect").html(options);
      },
      error: function(xhr) {
        console.log("getUsers erreur:", xhr.status, xhr.responseText);
      }
    });
  }

  // FONCTION: Charger la liste des conversations pour la barre latérale gauche
  function loadConversations() {
    $.ajax({
      url: API + "getConversations.php",
      method: "GET",
      dataType: "json",
      timeout: 8000,
      success: function(data) {
        let html = "";

        if (data.conversations && data.conversations.length > 0) {
          // Afficher chaque conversation avec nom d'utilisateur, aperçu et nombre de messages non lus
          data.conversations.forEach(function(conv) {
            // Marquer comme actif si cette conversation est actuellement sélectionnée
            const activeClass = (currentConversationId == conv.idTrainer && !isGlobalChat) ? "active" : "";
            // Afficher le badge non lu s'il y a des messages non lus
            const unreadBadge = (conv.unreadCount > 0) ? `<span class="unread-badge">${conv.unreadCount}</span>` : "";

            html += `
              <div class="conversation-item ${activeClass}" data-trainer-id="${conv.idTrainer}">
                <div class="username">${escapeHtml(conv.username)} ${unreadBadge}</div>
                <div class="preview">${escapeHtml(conv.lastMessage || "")}</div>
              </div>`;
          });
        } else {
          // Afficher un message quand il n'y a pas de conversations
          html = `<div class="no-contacts">Pas encore de conversations</div>`;
        }

        $("#conversationsList").html(html);
      },
      error: function(xhr) {
        console.log("getConversations erreur:", xhr.status, xhr.responseText);
      }
    });
  }

  // FONCTION: Ouvrir une conversation de message direct
  function openConversation(trainerId, username) {
    isGlobalChat = false;                          // Passer au mode messagerie directe
    currentConversationId = trainerId;             // Définir la conversation actuelle
    
    // Mettre à jour l'en-tête du chat avec le nom d'utilisateur du destinataire
    $("#chatTitle").text("Chat avec " + username);
    // Supprimer l'état actif du chat global
    $("#globalChatSidebar").removeClass("active");

    // Activer le champ de saisie et définir le focus
    $("#messageInput").prop("disabled", false).focus();
    $("#sendMessageBtn").prop("disabled", false);

    // Mettre à jour l'état actif visuel
    $(".conversation-item").removeClass("active");
    $(`.conversation-item[data-trainer-id="${trainerId}"]`).addClass("active");

    // Marquer les messages comme lus (AJAX sans blocage)
    $.ajax({
      url: API + "markMessagesAsRead.php",
      method: "POST",
      data: { trainerId: trainerId }
    });

    // Charger et afficher les messages pour cette conversation
    loadMessages(trainerId);
  }

  // FONCTION: Ouvrir la salle de chat global
  function openGlobalChat() {
    isGlobalChat = true;               // Passer au mode chat global
    currentConversationId = null;      // Aucune conversation spécifique

    // Mettre à jour l'en-tête
    $("#chatTitle").text("🌍 Chat Global - Tout le monde");
    $("#globalChatSidebar").addClass("active");
    // Supprimer l'état actif des messages directs
    $(".conversation-item").removeClass("active");

    // Activer le champ de saisie
    $("#messageInput").prop("disabled", false).focus();
    $("#sendMessageBtn").prop("disabled", false);

    console.log('openGlobalChat: loading global chat...');
    // Charger et afficher les messages du chat global
    loadGlobalChat();
  }

  // FONCTION: Charger les messages directs pour une conversation
  function loadMessages(trainerId) {
    $.ajax({
      url: API + "getMessages.php",
      method: "GET",
      dataType: "json",
      data: { trainerId: trainerId },
      timeout: 8000,
      success: function(data) {
        let html = "";
        const msgs = (data && data.messages) ? data.messages : [];

        if (msgs.length > 0) {
          // Afficher chaque message avec style basé sur le fait qu'il a été envoyé ou reçu
          msgs.forEach(function(msg) {
            const messageClass = msg.isSent ? "sent" : "received";
            // Formater l'horodatage au format HH:MM
            const time = new Date(msg.createdAt).toLocaleTimeString([], {hour:"2-digit", minute:"2-digit"});

            html += `
              <div class="message ${messageClass}">
                <div>
                  <div class="message-bubble">${escapeHtml(msg.messageText)}</div>
                  <div class="message-time">${time}</div>
                </div>
              </div>`;
          });
        } else {
          // Afficher un espace réservé quand il n'y a pas de messages
          html = `<div class="no-conversation">Pas encore de messages</div>`;
        }

        // Mettre à jour l'affichage des messages et faire défiler vers le bas
        $("#messagesContainer").html(html);
        $("#messagesContainer").scrollTop($("#messagesContainer")[0].scrollHeight);
      },
      error: function(xhr) {
        console.log("getMessages erreur:", xhr.status, xhr.responseText);
      }
    });
  }

  // FONCTION: Charger les messages du chat global (public)
  function loadGlobalChat() {
    $.ajax({
      url: API + "getGlobalChat.php",
      method: "GET",
      dataType: "json",
      timeout: 8000,
      success: function(data) {
        console.log('getGlobalChat success:', data);
        let html = "";
        const msgs = (data && data.messages) ? data.messages : [];

        if (msgs.length > 0) {
          // Afficher chaque message du chat global avec le nom de l'auteur et le texte
          msgs.forEach(function(msg) {
            const messageClass = msg.isSent ? "sent" : "received";
            const time = new Date(msg.createdAt).toLocaleTimeString([], {hour:"2-digit", minute:"2-digit"});

            html += `
              <div class="message ${messageClass}">
                <div>
                  <div class="message-bubble">
                    <strong>${escapeHtml(msg.username)}</strong><br>
                    ${escapeHtml(msg.messageText)}
                  </div>
                  <div class="message-time">${time}</div>
                </div>
              </div>`;
          });
        } else {
          // Afficher un espace réservé quand il n'y a pas de messages
          html = `<div class="no-conversation">Pas encore de messages dans le chat global</div>`;
        }

        // Mettre à jour l'affichage des messages et faire défiler vers le bas
        $("#messagesContainer").html(html);
        $("#messagesContainer").scrollTop($("#messagesContainer")[0].scrollHeight);
      },
      error: function(xhr) {
        console.log("getGlobalChat erreur:", xhr.status, xhr.responseText);
        // Afficher une alerte légère pour aider au debug si la requête échoue
        try {
          const body = xhr.responseText || '';
          console.warn('getGlobalChat failed body:', body);
        } catch (e) { /* ignore */ }
      }
    });
  }

  // FONCTION: Envoyer un message direct
  function sendMessage() {
    const text = ($("#messageInput").val() || "").trim();
    // Valider que le message n'est pas vide et qu'une conversation est sélectionnée
    if (!text || !currentConversationId) return;

    // Désactiver le bouton d'envoi pendant la transmission
    $("#sendMessageBtn").prop("disabled", true);

    $.ajax({
      url: API + "sendMessage.php",
      method: "POST",
      dataType: "json",
      timeout: 8000,
      data: { receiverId: currentConversationId, messageText: text },
      success: function(res) {
        if (!res || !res.success) {
          alert((res && res.message) ? res.message : "Error sending message");
          return;
        }

        // Clear input field and reset typing flag
        $("#messageInput").val("");
        isTyping = false;

        // Refresh messages and conversations
        loadMessages(currentConversationId);
        loadConversations();
      },
      error: function(xhr) {
        console.log("sendMessage error:", xhr.status, xhr.responseText);
        alert("sendMessage error: " + xhr.status);
      },
      complete: function() {
        // Re-enable send button and focus input
        $("#sendMessageBtn").prop("disabled", false);
        $("#messageInput").focus();
      }
    });
  }

  // FUNCTION: Send a global chat message (public)
  function sendGlobalMessage() {
    const text = ($("#messageInput").val() || "").trim();
    // Validate message is not empty
    if (!text) return;

    // Disable send button during transmission
    $("#sendMessageBtn").prop("disabled", true);

    $.ajax({
      url: API + "sendGlobalChat.php",
      method: "POST",
      dataType: "json",
      timeout: 8000,
      data: { messageText: text },
      success: function(res) {
        if (!res || !res.success) {
          alert((res && res.message) ? res.message : "Error sending message");
          return;
        }

        // Clear input field and reset typing flag
        $("#messageInput").val("");
        isTyping = false;

        // Refresh global chat messages
        loadGlobalChat();
      },
      error: function(xhr) {
        console.log("sendGlobalChat error:", xhr.status, xhr.responseText);
        alert("sendGlobalChat error: " + xhr.status);
      },
      complete: function() {
        // Re-enable send button and focus input
        $("#sendMessageBtn").prop("disabled", false);
        $("#messageInput").focus();
      }
    });
  }

  // UTILITY FUNCTION: Escape HTML to prevent XSS attacks
  function escapeHtml(text) {
    text = String(text ?? "");
    // Replace dangerous HTML characters with safe entities
    const map = {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'};
    return text.replace(/[&<>"']/g, m => map[m]);
  }
</script>

</body>
</html>
