(function(){
  function qs(selector, root){ return (root||document).querySelector(selector); }
  function initAjaxLinks(){
    document.querySelectorAll('a.ajax-link').forEach(function(link){
      // Avoid duplicate binding
      if(link.dataset.ajaxBound === '1') return;
      link.dataset.ajaxBound = '1';
      link.addEventListener('click', function(e){
        if(e.button !== 0 || e.metaKey || e.ctrlKey || e.shiftKey || e.altKey) return;
        e.preventDefault();
        const url = link.getAttribute('href');
        if(!url) return;
        const column = qs('.col-md-9');
        const card = qs('.col-md-9 .card.content-border');
        const cardBody = qs('.col-md-9 .card.content-border .card-body');
        const container = cardBody || card || column;
        if(!container){ window.location.href = url; return; }
        // Preserve height to prevent layout jump
        const h = container.offsetHeight;
        container.style.minHeight = h + 'px';
        container.style.opacity = '0';
        fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
          .then(function(res){ return res.text(); })
          .then(function(html){
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            // Prefer new card-body to limit DOM replacement
            let newCardBody = qs('.col-md-9 .card.content-border .card-body', doc);
            let newCard = qs('.col-md-9 .card.content-border', doc);
            let newColumn = qs('.col-md-9', doc);
            if(newCardBody && cardBody){
              cardBody.innerHTML = newCardBody.innerHTML;
            } else if(newCard && card){
              card.outerHTML = newCard.outerHTML;
            } else if(newColumn && column){
              column.innerHTML = newColumn.innerHTML;
            } else {
              container.innerHTML = html;
            }
            window.history.pushState({ partial: true }, '', url);
            // Re-bind ajax links after content swap
            initAjaxLinks();
          })
          .catch(function(err){ console.error('AJAX load failed', err); window.location.href = url; })
          .finally(function(){
            // Smooth fade-in
            requestAnimationFrame(function(){
              container.style.opacity = '1';
              container.style.transition = 'opacity 160ms ease';
              // Release minHeight after transition
              setTimeout(function(){ container.style.minHeight = ''; }, 200);
            });
          });
      });
    });
  }
  window.addEventListener('popstate', function(){
    const url = window.location.href;
    const column = qs('.col-md-9');
    const cardBody = qs('.col-md-9 .card.content-border .card-body');
    const container = cardBody || column;
    if(!container) return;
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' }})
      .then(function(res){ return res.text(); })
      .then(function(html){
        const parser = new DOMParser();
        const doc = parser.parseFromString(html, 'text/html');
        let newCardBody = qs('.col-md-9 .card.content-border .card-body', doc);
        let newColumn = qs('.col-md-9', doc);
        if(newCardBody && cardBody){
          cardBody.innerHTML = newCardBody.innerHTML;
        } else if(newColumn && column){
          column.innerHTML = newColumn.innerHTML;
        } else {
          container.innerHTML = html;
        }
        initAjaxLinks();
      })
      .catch(function(err){ console.error('AJAX popstate failed', err); });
  });
  document.addEventListener('DOMContentLoaded', function(){ initAjaxLinks(); });
})();
