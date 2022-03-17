<?php

namespace App\Entity;

use App\Repository\PublicacionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PublicacionRepository::class)
 */
class Publicacion
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $fecha;

    /**
     * @ORM\Column(type="text")
     */
    private $cuerpo;

    /**
     * @ORM\Column(type="text")
     */
    private $titulo;

    /**
     * @ORM\OneToMany(targetEntity=Imagen::class, mappedBy="publicacion")
     */
    private $imagenes;

    /**
     * @ORM\OneToMany(targetEntity=Archivo::class, mappedBy="publicacion")
     */
    private $archivos;

    /**
     * @ORM\ManyToMany(targetEntity=Menu::class, mappedBy="publicaciones")
     */
    private $menus;

    /**
     * @ORM\Column(type="boolean")
     */
    private $flagNoticia;

    /**
     * @ORM\Column(type="text")
     */
    private $descripcion;

    /**
     * @ORM\OneToMany(targetEntity=Banner::class, mappedBy="publicacion")
     */
    private $banners;

    public function __construct()
    {
        $this->imagenes = new ArrayCollection();
        $this->archivos = new ArrayCollection();
        $this->menus = new ArrayCollection();
        $this->banners = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFecha(): ?\DateTimeInterface
    {
        return $this->fecha;
    }

    public function setFecha(\DateTimeInterface $fecha): self
    {
        $this->fecha = $fecha;

        return $this;
    }

    public function getCuerpo(): ?string
    {
        return $this->cuerpo;
    }

    public function setCuerpo(string $cuerpo): self
    {
        $this->cuerpo = $cuerpo;

        return $this;
    }

    public function getTitulo(): ?string
    {
        return $this->titulo;
    }

    public function setTitulo(string $titulo): self
    {
        $this->titulo = $titulo;

        return $this;
    }

    /**
     * @return Collection<int, Imagen>
     */
    public function getImagenes(): Collection
    {
        return $this->imagenes;
    }

    public function addImagen(Imagen $imagene): self
    {
        if (!$this->imagenes->contains($imagene)) {
            $this->imagenes[] = $imagene;
            $imagene->setPublicacion($this);
        }

        return $this;
    }

    public function removeImagen(Imagen $imagene): self
    {
        if ($this->imagenes->removeElement($imagene)) {
            // set the owning side to null (unless already changed)
            if ($imagene->getPublicacion() === $this) {
                $imagene->setPublicacion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Archivo>
     */
    public function getArchivos(): Collection
    {
        return $this->archivos;
    }

    public function addArchivo(Archivo $archivo): self
    {
        if (!$this->archivos->contains($archivo)) {
            $this->archivos[] = $archivo;
            $archivo->setPublicacion($this);
        }

        return $this;
    }

    public function removeArchivo(Archivo $archivo): self
    {
        if ($this->archivos->removeElement($archivo)) {
            // set the owning side to null (unless already changed)
            if ($archivo->getPublicacion() === $this) {
                $archivo->setPublicacion(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Menu>
     */
    public function getMenus(): Collection
    {
        return $this->menus;
    }

    public function addMenu(Menu $menu): self
    {
        if (!$this->menus->contains($menu)) {
            $this->menus[] = $menu;
            $menu->addPublicacione($this);
        }

        return $this;
    }

    public function removeMenu(Menu $menu): self
    {
        if ($this->menus->removeElement($menu)) {
            $menu->removePublicacione($this);
        }

        return $this;
    }

    public function getFlagNoticia(): ?bool
    {
        return $this->flagNoticia;
    }

    public function setFlagNoticia(bool $flagNoticia): self
    {
        $this->flagNoticia = $flagNoticia;

        return $this;
    }

    public function getDescripcion(): ?string
    {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): self
    {
        $this->descripcion = $descripcion;

        return $this;
    }

    /**
     * @return Collection<int, Banner>
     */
    public function getBanners(): Collection
    {
        return $this->banners;
    }

    public function addBanner(Banner $banner): self
    {
        if (!$this->banners->contains($banner)) {
            $this->banners[] = $banner;
            $banner->setPublicacion($this);
        }

        return $this;
    }

    public function removeBanner(Banner $banner): self
    {
        if ($this->banners->removeElement($banner)) {
            // set the owning side to null (unless already changed)
            if ($banner->getPublicacion() === $this) {
                $banner->setPublicacion(null);
            }
        }

        return $this;
    }

    public function getTipoPublicacion() {

        if($this->getFlagNoticia() == 1) {
         return "Noticia";
        }
        else {
         return "Post";
        }
    }

    public function getNombreDescriptivo() {    
     return $nombreDescriptivo=$this->getTipoPublicacion().": ".$this->getTitulo();
    }

}
